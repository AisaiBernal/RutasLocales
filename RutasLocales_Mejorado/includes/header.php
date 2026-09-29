<?php
require_once __DIR__ . '/config.php';
$u = usuario();
$pagina = basename($_SERVER['SCRIPT_NAME']);
function nav_link($file, $texto, $icono) {
    global $pagina;
    $act = $pagina === $file ? ' class="activo"' : '';
    return "<a href=\"$file\"$act><i class=\"fa-solid $icono\"></i> $texto</a>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($titulo ?? 'RutasLocales') ?> · RutasLocales</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;800&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<script src="assets/app.js" defer></script>
</head>
<body class="<?= e($clase_body ?? '') ?>">
<header class="topbar">
    <a class="marca" href="index.php"><i class="fa-solid fa-mountain-sun"></i> RutasLocales</a>
    <button class="menu-btn" id="menuBtn" aria-label="Abrir menú" aria-expanded="false"><i class="fa-solid fa-bars"></i></button>
    <nav id="menu" class="menu">
        <?= nav_link('index.php', 'Explorar', 'fa-compass') ?>
        <?php if ($u): ?>
            <?= nav_link('dashboard.php', 'Mi panel', 'fa-gauge') ?>
            <?php if ($u['rol'] === 'guia'): ?><?= nav_link('mis_experiencias.php', 'Mis experiencias', 'fa-route') ?><?php endif; ?>
            <?php if ($u['rol'] === 'admin'): ?><?= nav_link('admin.php', 'Administración', 'fa-shield-halved') ?><?php endif; ?>
            <?= nav_link('perfil.php', 'Perfil', 'fa-user') ?>
            <a class="btn-salir" href="logout.php">Salir</a>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
            <a class="btn-nav" href="registro.php">Crear cuenta</a>
        <?php endif; ?>
    </nav>
</header>
<?php if (!empty($_SESSION['flash'])): ?>
<div class="flash-wrap">
    <?php foreach ($_SESSION['flash'] as [$tipo, $msg]): ?>
        <div class="flash flash-<?= e($tipo) ?>" role="status"><?= e($msg) ?></div>
    <?php endforeach; unset($_SESSION['flash']); ?>
</div>
<?php endif; ?>
