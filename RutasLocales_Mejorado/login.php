<?php
$titulo = 'Iniciar sesión'; $clase_body = 'pagina-auth';
require 'includes/header.php';
if (usuario()) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && password_verify($_POST['password'] ?? '', $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$row['id_usuario'], 'nombre' => $row['nombre'], 'rol' => $row['rol']];
        flash('Bienvenido, ' . $row['nombre'] . '.');
        redirect('dashboard.php');
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<main class="auth-caja">
    <h1>Iniciar sesión</h1>
    <?php if ($error): ?><div class="flash flash-error"><?= e($error) ?></div><?php endif; ?>
    <form method="POST" novalidate id="formLogin">
        <?= csrf_field() ?>
        <label>Correo electrónico
            <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autocomplete="email">
        </label>
        <label>Contraseña
            <span class="pass-wrap"><input type="password" name="password" required autocomplete="current-password">
            <button type="button" class="ver-pass" data-toggle-pass>mostrar</button></span>
        </label>
        <button class="btn btn-primario ancho" type="submit">Entrar</button>
    </form>
    <p class="aux">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    <p class="demo">Demo: admin@ / guia@ / turista@rutaslocales.com · Demo1234</p>
</main>
<?php require 'includes/footer.php'; ?>
