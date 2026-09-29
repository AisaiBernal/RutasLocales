<?php
$titulo = 'Mis experiencias';
require 'includes/header.php';
require_rol('guia');
$u = usuario();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {           // DELETE y activar/pausar (siempre sobre experiencias propias)
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['accion'] ?? '') === 'eliminar') {
        $pdo->prepare("DELETE FROM experiencias WHERE id_experiencia = ? AND id_guia = ?")->execute([$id, $u['id']]);
        flash('Experiencia eliminada.');
    } elseif (($_POST['accion'] ?? '') === 'alternar') {
        $pdo->prepare("UPDATE experiencias SET activa = 1 - activa WHERE id_experiencia = ? AND id_guia = ?")->execute([$id, $u['id']]);
        flash('Estado actualizado.');
    }
    redirect('mis_experiencias.php');
}
$stmt = $pdo->prepare("SELECT e.*, c.nombre AS categoria FROM experiencias e LEFT JOIN categorias c ON c.id_categoria = e.id_categoria WHERE e.id_guia = ? ORDER BY e.fecha_creacion DESC");
$stmt->execute([$u['id']]);
$lista = $stmt->fetchAll();
?>
<main class="contenedor">
    <div class="fila-titulo"><h1 class="titulo-pagina">Mis experiencias</h1>
        <a class="btn btn-primario" href="experiencia_form.php"><i class="fa-solid fa-plus"></i> Nueva experiencia</a></div>
    <input class="buscar-tabla" type="search" placeholder="Filtrar por título o lugar" data-live-filter="#tablaExp tbody tr">
    <div class="tabla-wrap"><table class="tabla" id="tablaExp">
        <thead><tr><th>Título</th><th>Categoría</th><th>Precio</th><th>Cupos</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($lista as $x): ?>
            <tr data-texto="<?= e(mb_strtolower($x['titulo'] . ' ' . $x['ubicacion'])) ?>">
                <td><strong><?= e($x['titulo']) ?></strong><br><span class="muted"><?= e($x['ubicacion']) ?></span></td>
                <td><?= e($x['categoria'] ?? '—') ?></td>
                <td><?= money($x['precio']) ?></td>
                <td><?= (int)$x['cupos_disponibles'] ?></td>
                <td><?= $x['activa'] ? '<span class="badge b-confirmada">Activa</span>' : '<span class="badge b-cancelada">Pausada</span>' ?></td>
                <td><div class="acciones">
                    <a class="btn-mini" href="experiencia_form.php?id=<?= $x['id_experiencia'] ?>">Editar</a>
                    <form method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $x['id_experiencia'] ?>"><input type="hidden" name="accion" value="alternar"><button class="btn-mini"><?= $x['activa'] ? 'Pausar' : 'Activar' ?></button></form>
                    <form method="POST" data-confirm="¿Eliminar «<?= e($x['titulo']) ?>» y sus reservas?"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $x['id_experiencia'] ?>"><input type="hidden" name="accion" value="eliminar"><button class="btn-mini mal">Eliminar</button></form>
                </div></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$lista): ?><tr><td colspan="6" class="vacio">Aún no has publicado nada.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
</main>
<?php require 'includes/footer.php'; ?>
