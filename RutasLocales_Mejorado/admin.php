<?php
$titulo = 'Administración';
require 'includes/header.php';
require_rol('admin');
$u = usuario();
$tab = in_array($_GET['tab'] ?? '', ['usuarios', 'experiencias', 'reservas', 'categorias'], true) ? $_GET['tab'] : 'usuarios';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $a = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($a === 'rol' && $id !== $u['id'] && in_array($_POST['rol'] ?? '', ['turista', 'guia', 'admin'], true)) {
        $pdo->prepare("UPDATE usuarios SET rol=? WHERE id_usuario=?")->execute([$_POST['rol'], $id]); flash('Rol actualizado.');
    } elseif ($a === 'borrar_usuario' && $id !== $u['id']) {
        $pdo->prepare("DELETE FROM usuarios WHERE id_usuario=?")->execute([$id]); flash('Usuario eliminado.');
    } elseif ($a === 'alternar_exp') {
        $pdo->prepare("UPDATE experiencias SET activa = 1 - activa WHERE id_experiencia=?")->execute([$id]); flash('Experiencia actualizada.');
    } elseif ($a === 'borrar_exp') {
        $pdo->prepare("DELETE FROM experiencias WHERE id_experiencia=?")->execute([$id]); flash('Experiencia eliminada.');
    } elseif ($a === 'crear_cat') {
        $n = trim($_POST['nombre'] ?? ''); $i = preg_match('/^fa-[a-z-]+$/', $_POST['icono'] ?? '') ? $_POST['icono'] : 'fa-compass';
        if (mb_strlen($n) < 3) flash('Nombre de categoría muy corto.', 'error');
        else { try { $pdo->prepare("INSERT INTO categorias (nombre,icono) VALUES (?,?)")->execute([$n, $i]); flash('Categoría creada.'); }
               catch (PDOException $e) { flash('Esa categoría ya existe.', 'error'); } }
    } elseif ($a === 'editar_cat') {
        $n = trim($_POST['nombre'] ?? ''); $i = preg_match('/^fa-[a-z-]+$/', $_POST['icono'] ?? '') ? $_POST['icono'] : 'fa-compass';
        if (mb_strlen($n) < 3) flash('Nombre de categoría muy corto.', 'error');
        else { try { $pdo->prepare("UPDATE categorias SET nombre=?, icono=? WHERE id_categoria=?")->execute([$n, $i, $id]); flash('Categoría actualizada.'); }
               catch (PDOException $e) { flash('Ya existe una categoría con ese nombre.', 'error'); } }
    } elseif ($a === 'borrar_reserva') {
        $r = $pdo->prepare("SELECT * FROM reservas WHERE id_reserva=?"); $r->execute([$id]); $r = $r->fetch();
        if ($r) {
            $pdo->beginTransaction();
            if ($r['estado'] !== 'cancelada')   // devuelve los cupos si la reserva estaba activa
                $pdo->prepare("UPDATE experiencias SET cupos_disponibles = cupos_disponibles + ? WHERE id_experiencia=?")->execute([$r['cantidad_personas'], $r['id_experiencia']]);
            $pdo->prepare("DELETE FROM reservas WHERE id_reserva=?")->execute([$id]);
            $pdo->commit(); flash('Reserva eliminada.');
        }
    } elseif ($a === 'borrar_cat') {
        $pdo->prepare("DELETE FROM categorias WHERE id_categoria=?")->execute([$id]); flash('Categoría eliminada.');
    }
    redirect("admin.php?tab=$tab");
}
function form_mini($accion, $id, $texto, $clase = '', $confirm = '') {
    $c = $confirm ? ' data-confirm="' . e($confirm) . '"' : '';
    return '<form method="POST"' . $c . '>' . csrf_field() . '<input type="hidden" name="accion" value="' . $accion . '"><input type="hidden" name="id" value="' . $id . '"><button class="btn-mini ' . $clase . '">' . $texto . '</button></form>';
}
?>
<main class="contenedor">
    <h1 class="titulo-pagina">Administración</h1>
    <div class="filtro-tabs">
        <?php foreach (['usuarios' => 'Usuarios', 'experiencias' => 'Experiencias', 'reservas' => 'Reservas', 'categorias' => 'Categorías'] as $k => $t): ?>
            <a class="tab <?= $tab === $k ? 'activo' : '' ?>" href="admin.php?tab=<?= $k ?>"><?= $t ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($tab === 'usuarios'):
        $q = trim($_GET['q'] ?? '');
        $s = $pdo->prepare("SELECT * FROM usuarios WHERE nombre LIKE ? OR email LIKE ? ORDER BY fecha_registro DESC");
        $s->execute(["%$q%", "%$q%"]); $filas = $s->fetchAll(); ?>
        <form method="GET" class="buscar-tabla-form"><input type="hidden" name="tab" value="usuarios"><input class="buscar-tabla" type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nombre o correo"></form>
        <div class="tabla-wrap"><table class="tabla"><thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Registro</th><th></th></tr></thead><tbody>
        <?php foreach ($filas as $f): $yo = (int)$f['id_usuario'] === $u['id']; ?>
            <tr><td><?= e($f['nombre']) ?></td><td><?= e($f['email']) ?></td>
            <td><?php if ($yo): ?><?= e($f['rol']) ?> (tú)<?php else: ?>
                <form method="POST" class="rol-form"><?= csrf_field() ?><input type="hidden" name="accion" value="rol"><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
                <select name="rol" data-autosubmit><?php foreach (['turista', 'guia', 'admin'] as $r): ?><option <?= $f['rol'] === $r ? 'selected' : '' ?>><?= $r ?></option><?php endforeach; ?></select></form>
            <?php endif; ?></td>
            <td><?= e(date('d/m/Y', strtotime($f['fecha_registro']))) ?></td>
            <td><?= $yo ? '' : form_mini('borrar_usuario', $f['id_usuario'], 'Eliminar', 'mal', '¿Eliminar a ' . $f['nombre'] . ' y todos sus datos?') ?></td></tr>
        <?php endforeach; ?></tbody></table></div>

    <?php elseif ($tab === 'experiencias'):
        $filas = $pdo->query("SELECT e.*, u.nombre AS guia FROM experiencias e JOIN usuarios u ON u.id_usuario = e.id_guia ORDER BY e.fecha_creacion DESC")->fetchAll(); ?>
        <div class="tabla-wrap"><table class="tabla"><thead><tr><th>Título</th><th>Guía</th><th>Precio</th><th>Cupos</th><th>Estado</th><th></th></tr></thead><tbody>
        <?php foreach ($filas as $f): ?>
            <tr><td><?= e($f['titulo']) ?></td><td><?= e($f['guia']) ?></td><td><?= money($f['precio']) ?></td><td><?= (int)$f['cupos_disponibles'] ?></td>
            <td><?= $f['activa'] ? '<span class="badge b-confirmada">Activa</span>' : '<span class="badge b-cancelada">Pausada</span>' ?></td>
            <td><div class="acciones"><?= form_mini('alternar_exp', $f['id_experiencia'], $f['activa'] ? 'Pausar' : 'Activar') ?><?= form_mini('borrar_exp', $f['id_experiencia'], 'Eliminar', 'mal', '¿Eliminar esta experiencia?') ?></div></td></tr>
        <?php endforeach; ?></tbody></table></div>

    <?php elseif ($tab === 'reservas'):
        $filas = $pdo->query("SELECT r.*, e.titulo, t.nombre AS turista FROM reservas r
            JOIN experiencias e ON e.id_experiencia = r.id_experiencia JOIN usuarios t ON t.id_usuario = r.id_turista
            ORDER BY r.fecha_creacion DESC")->fetchAll(); ?>
        <div class="tabla-wrap"><table class="tabla"><thead><tr><th>Experiencia</th><th>Turista</th><th>Fecha</th><th>Pers.</th><th>Total</th><th>Estado</th><th></th></tr></thead><tbody>
        <?php foreach ($filas as $f): ?>
            <tr><td><?= e($f['titulo']) ?></td><td><?= e($f['turista']) ?></td><td><?= e(date('d/m/Y', strtotime($f['fecha_reserva']))) ?></td>
            <td><?= (int)$f['cantidad_personas'] ?></td><td><?= money($f['total']) ?></td><td><?= estado_badge($f['estado']) ?></td>
            <td><?= form_mini('borrar_reserva', $f['id_reserva'], 'Eliminar', 'mal', '¿Eliminar esta reserva?') ?></td></tr>
        <?php endforeach; if (!$filas) echo '<tr><td colspan="7" class="vacio">Sin reservas.</td></tr>'; ?></tbody></table></div>

    <?php else:
        $filas = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM experiencias e WHERE e.id_categoria = c.id_categoria) usos FROM categorias c ORDER BY nombre")->fetchAll(); ?>
        <form method="POST" class="form-linea"><?= csrf_field() ?><input type="hidden" name="accion" value="crear_cat">
            <input name="nombre" placeholder="Nueva categoría" required minlength="3">
            <input name="icono" placeholder="Icono (fa-mug-hot)" value="fa-compass" pattern="fa-[a-z-]+">
            <button class="btn btn-primario">Agregar</button></form>
        <div class="tabla-wrap"><table class="tabla"><thead><tr><th>Nombre</th><th>Icono</th><th>Experiencias</th><th></th></tr></thead><tbody>
        <?php foreach ($filas as $f): ?>
            <tr>
            <td colspan="2"><form method="POST" class="form-linea fila-cat"><?= csrf_field() ?><input type="hidden" name="accion" value="editar_cat"><input type="hidden" name="id" value="<?= $f['id_categoria'] ?>">
                <input name="nombre" value="<?= e($f['nombre']) ?>" required minlength="3" aria-label="Nombre">
                <input name="icono" value="<?= e($f['icono']) ?>" pattern="fa-[a-z-]+" aria-label="Icono">
                <button class="btn-mini ok">Guardar</button></form></td>
            <td><?= (int)$f['usos'] ?></td>
            <td><?= form_mini('borrar_cat', $f['id_categoria'], 'Eliminar', 'mal', '¿Eliminar la categoría? Las experiencias quedarán sin categoría.') ?></td></tr>
        <?php endforeach; ?></tbody></table></div>
    <?php endif; ?>
</main>
<?php require 'includes/footer.php'; ?>
