<?php
// Conexión, sesión y funciones de ayuda compartidas por todas las páginas
ob_start();          // permite redirigir aunque ya se haya empezado a imprimir el HTML
session_start();

$host = 'localhost'; $db = 'rutas_locales'; $user = 'root'; $pass = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('No se pudo conectar a la base de datos. Revisa includes/config.php y que hayas importado database.sql.');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }          // evita XSS
function money($n) { return '$' . number_format((float)$n, 0, ',', '.'); }
function redirect($url) { header("Location: $url"); exit; }
function flash($msg, $tipo = 'ok') { $_SESSION['flash'][] = [$tipo, $msg]; }
function usuario() { return $_SESSION['user'] ?? null; }

function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); die('Solicitud no válida.'); }
}
function require_login() {
    if (!usuario()) { flash('Inicia sesión para continuar.', 'error'); redirect('login.php'); }
}
function require_rol(...$roles) {
    require_login();
    if (!in_array(usuario()['rol'], $roles, true)) { http_response_code(403); die('No tienes permiso para ver esta página.'); }
}
function estado_badge($estado) { return '<span class="badge b-' . e($estado) . '">' . e(ucfirst($estado)) . '</span>'; }
