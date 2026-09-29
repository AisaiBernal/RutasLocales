<?php
$titulo = 'Crear cuenta'; $clase_body = 'pagina-auth';
require 'includes/header.php';
if (usuario()) redirect('dashboard.php');
$errores = [];
$v = ['nombre' => '', 'email' => '', 'rol' => 'turista'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $v['nombre'] = trim($_POST['nombre'] ?? '');
    $v['email'] = trim($_POST['email'] ?? '');
    $v['rol'] = $_POST['rol'] ?? '';
    $pass = $_POST['password'] ?? '';
    if (mb_strlen($v['nombre']) < 3)                       $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL))   $errores[] = 'Correo no válido.';
    if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) $errores[] = 'La contraseña necesita 8+ caracteres, con letras y números.';
    if ($pass !== ($_POST['password2'] ?? ''))             $errores[] = 'Las contraseñas no coinciden.';
    if (!in_array($v['rol'], ['turista', 'guia'], true))   $errores[] = 'Rol no válido.';   // nunca se puede elegir admin
    if (!$errores) {
        $c = $pdo->prepare("SELECT 1 FROM usuarios WHERE email = ?"); $c->execute([$v['email']]);
        if ($c->fetch()) $errores[] = 'Ese correo ya está registrado.';
    }
    if (!$errores) {
        $pdo->prepare("INSERT INTO usuarios (nombre,email,password,rol) VALUES (?,?,?,?)")
            ->execute([$v['nombre'], $v['email'], password_hash($pass, PASSWORD_DEFAULT), $v['rol']]);
        flash('Cuenta creada. Ya puedes iniciar sesión.');
        redirect('login.php');
    }
}
?>
<main class="auth-caja">
    <h1>Crear cuenta</h1>
    <?php foreach ($errores as $er): ?><div class="flash flash-error"><?= e($er) ?></div><?php endforeach; ?>
    <form method="POST" novalidate id="formRegistro">
        <?= csrf_field() ?>
        <label>Nombre completo <input type="text" name="nombre" value="<?= e($v['nombre']) ?>" required minlength="3"></label>
        <label>Correo electrónico <input type="email" name="email" value="<?= e($v['email']) ?>" required></label>
        <label>Contraseña
            <span class="pass-wrap"><input type="password" name="password" id="password" required minlength="8">
            <button type="button" class="ver-pass" data-toggle-pass>mostrar</button></span>
            <span class="fuerza" id="fuerza"><i></i></span>
        </label>
        <label>Repite la contraseña <input type="password" name="password2" id="password2" required></label>
        <label>¿Qué quieres hacer?
            <select name="rol">
                <option value="turista" <?= $v['rol'] === 'turista' ? 'selected' : '' ?>>Explorar y reservar planes</option>
                <option value="guia" <?= $v['rol'] === 'guia' ? 'selected' : '' ?>>Ofrecer mis tours como guía</option>
            </select>
        </label>
        <p class="error-campo" id="errForm" hidden></p>
        <button class="btn btn-primario ancho" type="submit">Completar registro</button>
    </form>
    <p class="aux">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</main>
<?php require 'includes/footer.php'; ?>
