<?php
$titulo = 'Experiencia';
require 'includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT e.*, c.nombre AS categoria, u.nombre AS guia, u.bio
    FROM experiencias e JOIN usuarios u ON u.id_usuario = e.id_guia
    LEFT JOIN categorias c ON c.id_categoria = e.id_categoria WHERE e.id_experiencia = ? AND e.activa = 1");
$stmt->execute([$id]);
$x = $stmt->fetch();
if (!$x) { http_response_code(404); echo '<main class="contenedor"><p class="vacio">Experiencia no encontrada. <a href="index.php">Volver</a></p></main>'; require 'includes/footer.php'; exit; }
?>
<main class="contenedor detalle">
    <section>
        <div class="detalle-img" style="<?= $x['imagen_url'] ? "background-image:url('" . e($x['imagen_url']) . "')" : '' ?>"></div>
        <h1><?= e($x['titulo']) ?></h1>
        <p class="meta"><i class="fa-solid fa-location-dot"></i> <?= e($x['ubicacion']) ?> · <i class="fa-regular fa-clock"></i> <?= (int)$x['duracion_horas'] ?> horas · <?= e($x['categoria'] ?? 'General') ?></p>
        <p class="texto-largo"><?= nl2br(e($x['descripcion'])) ?></p>
        <div class="caja-guia"><strong>Tu guía: <?= e($x['guia']) ?></strong><?php if ($x['bio']): ?><p><?= e($x['bio']) ?></p><?php endif; ?></div>
    </section>

    <aside class="panel-reserva">
        <p class="precio grande"><?= money($x['precio']) ?> <small>/ persona</small></p>
        <p class="meta"><?= (int)$x['cupos_disponibles'] ?> cupos disponibles</p>
        <?php if (!usuario()): ?>
            <a class="btn btn-primario ancho" href="login.php">Inicia sesión para reservar</a>
        <?php elseif (usuario()['rol'] !== 'turista'): ?>
            <p class="aviso">Solo las cuentas de turista pueden reservar.</p>
        <?php elseif ($x['cupos_disponibles'] < 1): ?>
            <p class="aviso">Sin cupos por ahora.</p>
        <?php else: ?>
        <form id="formReserva" method="POST" action="reservar.php" novalidate data-precio="<?= (float)$x['precio'] ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id_experiencia" value="<?= $x['id_experiencia'] ?>">
            <label>Personas
                <input type="number" id="cantidad" name="cantidad" min="1" max="<?= (int)$x['cupos_disponibles'] ?>" value="1" required>
            </label>
            <label>Fecha
                <input type="date" id="fecha" name="fecha" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
            </label>
            <p class="total">Total estimado: <strong id="totalPagar"><?= money($x['precio']) ?></strong></p>
            <p class="error-campo" id="errReserva" hidden></p>
            <button class="btn btn-primario ancho" type="submit">Confirmar reserva</button>
        </form>
        <?php endif; ?>
    </aside>
</main>
<?php require 'includes/footer.php'; ?>
