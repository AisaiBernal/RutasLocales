<?php
$titulo = 'Mi panel';
require 'includes/header.php';
require_login();
$u = usuario();

// Botones de acción sobre una reserva (POST + CSRF)
function acciones_reserva($r, $rol) {
    if ($r['estado'] === 'cancelada') return '<span class="muted">—</span>';
    $h = '';
    if ($rol !== 'turista' && $r['estado'] === 'pendiente')
        $h .= '<form method="POST" action="reserva_estado.php">' . csrf_field() . '<input type="hidden" name="id_reserva" value="' . $r['id_reserva'] . '"><input type="hidden" name="accion" value="confirmar"><button class="btn-mini ok">Confirmar</button></form>';
    $h .= '<form method="POST" action="reserva_estado.php" data-confirm="¿Cancelar esta reserva?">' . csrf_field() . '<input type="hidden" name="id_reserva" value="' . $r['id_reserva'] . '"><input type="hidden" name="accion" value="cancelar"><button class="btn-mini mal">Cancelar</button></form>';
    return '<div class="acciones">' . $h . '</div>';
}
?>
<main class="contenedor">
<h1 class="titulo-pagina">Hola, <?= e(explode(' ', $u['nombre'])[0]) ?></h1>

<?php if ($u['rol'] === 'turista'):
    $st = $pdo->prepare("SELECT COUNT(*) total,
        SUM(estado='pendiente') pend, SUM(estado='confirmada') conf,
        COALESCE(SUM(CASE WHEN estado<>'cancelada' THEN total END),0) gasto
        FROM reservas WHERE id_turista = ?");
    $st->execute([$u['id']]); $s = $st->fetch();
    $rs = $pdo->prepare("SELECT r.*, e.titulo, e.ubicacion FROM reservas r JOIN experiencias e ON e.id_experiencia = r.id_experiencia WHERE r.id_turista = ? ORDER BY r.fecha_reserva DESC");
    $rs->execute([$u['id']]); $reservas = $rs->fetchAll();
?>
    <section class="stats">
        <div class="stat"><span data-count="<?= (int)$s['total'] ?>">0</span><p>Reservas totales</p></div>
        <div class="stat"><span data-count="<?= (int)$s['pend'] ?>">0</span><p>Pendientes</p></div>
        <div class="stat"><span data-count="<?= (int)$s['conf'] ?>">0</span><p>Confirmadas</p></div>
        <div class="stat"><span><?= money($s['gasto']) ?></span><p>Inversión activa</p></div>
    </section>
    <h2>Mis reservas</h2>
    <?php include 'includes/tabla_reservas.php'; ?>
    <p><a class="btn btn-primario" href="index.php">Explorar más experiencias</a></p>

<?php elseif ($u['rol'] === 'guia'):
    $st = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM experiencias WHERE id_guia = ? AND activa = 1) exp_act,
        COUNT(r.id_reserva) total, COALESCE(SUM(r.estado='pendiente'),0) pend,
        COALESCE(SUM(CASE WHEN r.estado='confirmada' THEN r.total END),0) ingresos
        FROM reservas r JOIN experiencias e ON e.id_experiencia = r.id_experiencia WHERE e.id_guia = ?");
    $st->execute([$u['id'], $u['id']]); $s = $st->fetch();
    $g = $pdo->prepare("SELECT e.titulo, COUNT(r.id_reserva) n FROM experiencias e
        LEFT JOIN reservas r ON r.id_experiencia = e.id_experiencia AND r.estado <> 'cancelada'
        WHERE e.id_guia = ? GROUP BY e.id_experiencia, e.titulo ORDER BY n DESC");
    $g->execute([$u['id']]); $barras = $g->fetchAll(); $maxb = max(1, ...array_column($barras ?: [['n' => 1]], 'n'));
    $rs = $pdo->prepare("SELECT r.*, e.titulo, t.nombre AS turista FROM reservas r
        JOIN experiencias e ON e.id_experiencia = r.id_experiencia JOIN usuarios t ON t.id_usuario = r.id_turista
        WHERE e.id_guia = ? ORDER BY FIELD(r.estado,'pendiente','confirmada','cancelada'), r.fecha_reserva");
    $rs->execute([$u['id']]); $reservas = $rs->fetchAll();
?>
    <section class="stats">
        <div class="stat"><span data-count="<?= (int)$s['exp_act'] ?>">0</span><p>Experiencias activas</p></div>
        <div class="stat"><span data-count="<?= (int)$s['pend'] ?>">0</span><p>Por confirmar</p></div>
        <div class="stat"><span data-count="<?= (int)$s['total'] ?>">0</span><p>Reservas recibidas</p></div>
        <div class="stat"><span><?= money($s['ingresos']) ?></span><p>Ingresos confirmados</p></div>
    </section>
    <section class="bloque">
        <h2>Reservas por experiencia</h2>
        <?php foreach ($barras as $b): ?>
            <div class="barra"><span><?= e($b['titulo']) ?></span><div><i style="width:<?= round($b['n'] / $maxb * 100) ?>%"></i></div><b><?= (int)$b['n'] ?></b></div>
        <?php endforeach; if (!$barras) echo '<p class="vacio">Aún no publicas experiencias.</p>'; ?>
    </section>
    <h2>Solicitudes de reserva</h2>
    <?php include 'includes/tabla_reservas.php'; ?>
    <p><a class="btn btn-primario" href="mis_experiencias.php">Gestionar mis experiencias</a></p>

<?php else: // admin
    $s = $pdo->query("SELECT (SELECT COUNT(*) FROM usuarios) usuarios, (SELECT COUNT(*) FROM experiencias) exps,
        (SELECT COUNT(*) FROM reservas) reservas,
        (SELECT COALESCE(SUM(total),0) FROM reservas WHERE estado='confirmada') ingresos")->fetch();
    $por_estado = $pdo->query("SELECT estado, COUNT(*) n FROM reservas GROUP BY estado")->fetchAll(PDO::FETCH_KEY_PAIR);
    $maxb = max(1, ...array_values($por_estado ?: [1]));
    $reservas = $pdo->query("SELECT r.*, e.titulo, t.nombre AS turista FROM reservas r
        JOIN experiencias e ON e.id_experiencia = r.id_experiencia JOIN usuarios t ON t.id_usuario = r.id_turista
        ORDER BY r.fecha_creacion DESC LIMIT 10")->fetchAll();
?>
    <section class="stats">
        <div class="stat"><span data-count="<?= (int)$s['usuarios'] ?>">0</span><p>Usuarios</p></div>
        <div class="stat"><span data-count="<?= (int)$s['exps'] ?>">0</span><p>Experiencias</p></div>
        <div class="stat"><span data-count="<?= (int)$s['reservas'] ?>">0</span><p>Reservas</p></div>
        <div class="stat"><span><?= money($s['ingresos']) ?></span><p>Ingresos confirmados</p></div>
    </section>
    <section class="bloque">
        <h2>Reservas por estado</h2>
        <?php foreach (['pendiente', 'confirmada', 'cancelada'] as $es): $n = (int)($por_estado[$es] ?? 0); ?>
            <div class="barra"><span><?= ucfirst($es) ?></span><div><i class="b-<?= $es ?>" style="width:<?= round($n / $maxb * 100) ?>%"></i></div><b><?= $n ?></b></div>
        <?php endforeach; ?>
    </section>
    <h2>Últimas reservas</h2>
    <?php include 'includes/tabla_reservas.php'; ?>
    <p><a class="btn btn-primario" href="admin.php">Ir a administración</a></p>
<?php endif; ?>
</main>
<?php require 'includes/footer.php'; ?>
