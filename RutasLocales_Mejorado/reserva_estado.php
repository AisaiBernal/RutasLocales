<?php
// Cambia el estado de una reserva (confirmar / cancelar) respetando permisos
require 'includes/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('dashboard.php');
csrf_check();

$u = usuario();
$id = (int)($_POST['id_reserva'] ?? 0);
$accion = $_POST['accion'] ?? '';

$s = $pdo->prepare("SELECT r.*, e.id_guia FROM reservas r JOIN experiencias e ON e.id_experiencia = r.id_experiencia WHERE r.id_reserva = ?");
$s->execute([$id]);
$r = $s->fetch();

$puede = $r && (
    $u['rol'] === 'admin' ||
    ($u['rol'] === 'guia' && (int)$r['id_guia'] === $u['id']) ||
    ($u['rol'] === 'turista' && (int)$r['id_turista'] === $u['id'] && $accion === 'cancelar')
);
if (!$puede || !in_array($accion, ['confirmar', 'cancelar'], true) || $r['estado'] === 'cancelada') {
    flash('No se pudo actualizar la reserva.', 'error'); redirect('dashboard.php');
}

$pdo->beginTransaction();
if ($accion === 'confirmar') {
    $pdo->prepare("UPDATE reservas SET estado='confirmada' WHERE id_reserva = ?")->execute([$id]);
} else {
    $pdo->prepare("UPDATE reservas SET estado='cancelada' WHERE id_reserva = ?")->execute([$id]);
    $pdo->prepare("UPDATE experiencias SET cupos_disponibles = cupos_disponibles + ? WHERE id_experiencia = ?")
        ->execute([$r['cantidad_personas'], $r['id_experiencia']]);   // devuelve los cupos
}
$pdo->commit();
flash($accion === 'confirmar' ? 'Reserva confirmada.' : 'Reserva cancelada.');
redirect('dashboard.php');
