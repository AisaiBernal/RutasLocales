<?php
$titulo = 'Perfil';
require 'includes/header.php';
require_login();
$u = usuario();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['accion'] ?? '') === 'datos') {
        $nombre = trim($_POST['nombre'] ?? ''); $tel = trim($_POST['telefono'] ?? ''); $bio = trim($_POST['bio'] ?? '');
        if (mb_strlen($nombre) < 3) $errores[] = 'El nombre debe tener al menos 3 caracteres.';
        if ($tel !== '' && !preg_match('/^[0-9+\s-]{7,20}$/', $tel)) $errores[] = 'Teléfono no válido.';
        if (mb_strlen($bio) > 300) $errores[] = 'La bio admite máximo 300 caracteres.';
        if (!$errores) {
            $pdo->prepare("UPDATE usuarios SET nombre=?, telefono=?, bio=? WHERE id_usuario=?")->execute([$nombre, $tel ?: null, $bio ?: null, $u['id']]);
            $_SESSION['user']['nombre'] = $nombre;
            flash('Perfil actualizado.'); redirect('perfil.php');
        }
    } elseif (($_POST['accion'] ?? '') === 'clave') {
        $h = $pdo->prepare("SELECT password FROM usuarios WHERE id_usuario = ?"); $h->execute([$u['id']]);
        $n = $_POST['nueva'] ?? '';
        if (!password_verify($_POST['actual'] ?? '', $h->fetchColumn())) $errores[] = 'La contraseña actual no es correcta.';
        if (strlen($n) < 8 || !preg_match('/[A-Za-z]/', $n) || !preg_match('/\d/', $n)) $errores[] = 'La nueva contraseña necesita 8+ caracteres, con letras y números.';
        if ($n !== ($_POST['nueva2'] ?? '')) $errores[] = 'Las contraseñas nuevas no coinciden.';
        if (!$errores) {
            $pdo->prepare("UPDATE usuarios SET password=? WHERE id_usuario=?")->execute([password_hash($n, PASSWORD_DEFAULT), $u['id']]);
            flash('Contraseña cambiada.'); redirect('perfil.php');
        }
    }
}
$f = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?"); $f->execute([$u['id']]); $p = $f->fetch();
?>
<main class="contenedor estrecho">
    <h1 class="titulo-pagina">Mi perfil</h1>
    <?php foreach ($errores as $er): ?><div class="flash flash-error"><?= e($er) ?></div><?php endforeach; ?>
    <p class="meta">Rol: <?= e($p['rol']) ?> · Miembro desde <?= e(date('d/m/Y', strtotime($p['fecha_registro']))) ?></p>
    <form method="POST" class="form-grid bloque" novalidate>
        <?= csrf_field() ?><input type="hidden" name="accion" value="datos">
        <label class="full">Correo (no editable) <input value="<?= e($p['email']) ?>" disabled></label>
        <label>Nombre <input name="nombre" value="<?= e($p['nombre']) ?>" required></label>
        <label>Teléfono <input name="telefono" value="<?= e($p['telefono']) ?>"></label>
        <label class="full">Sobre mí <textarea name="bio" rows="3" maxlength="300" data-counter><?= e($p['bio']) ?></textarea><small class="contador"></small></label>
        <div class="full"><button class="btn btn-primario">Guardar cambios</button></div>
    </form>
    <h2>Cambiar contraseña</h2>
    <form method="POST" class="form-grid bloque" novalidate>
        <?= csrf_field() ?><input type="hidden" name="accion" value="clave">
        <label class="full">Contraseña actual <input type="password" name="actual" required></label>
        <label>Nueva <input type="password" name="nueva" required minlength="8"></label>
        <label>Repite la nueva <input type="password" name="nueva2" required></label>
        <div class="full"><button class="btn btn-borde">Actualizar contraseña</button></div>
    </form>
</main>
<?php require 'includes/footer.php'; ?>
