<?php
// Acción principal del usuario: crear una reserva (Formulario -> PHP -> MySQL -> Resultado)
require 'includes/config.php';
require_rol('turista');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
csrf_check();

$id = (int)($_POST['id_experiencia'] ?? 0);
$cant = (int)($_POST['cantidad'] ?? 0);
$fecha = $_POST['fecha'] ?? '';
$d = DateTime::createFromFormat('Y-m-d', $fecha);
$fecha_ok = $d && $d->format('Y-m-d') === $fecha && $fecha > date('Y-m-d');

$pdo->beginTransaction();
try {
    $s = $pdo->prepare("SELECT * FROM experiencias WHERE id_experiencia = ? AND activa = 1 FOR UPDATE");
    $s->execute([$id]);
    $x = $s->fetch();
    if (!$x)                          throw new Exception('La experiencia no existe.');
    if ($cant < 1)                    throw new Exception('Debes reservar al menos 1 persona.');
    if ($cant > $x['cupos_disponibles']) throw new Exception('Solo quedan ' . $x['cupos_disponibles'] . ' cupos.');
    if (!$fecha_ok)                   throw new Exception('Elige una fecha futura válida.');

    $pdo->prepare("INSERT INTO reservas (id_turista,id_experiencia,cantidad_personas,fecha_reserva,total) VALUES (?,?,?,?,?)")
        ->execute([usuario()['id'], $id, $cant, $fecha, $cant * $x['precio']]);
    $pdo->prepare("UPDATE experiencias SET cupos_disponibles = cupos_disponibles - ? WHERE id_experiencia = ?")->execute([$cant, $id]);
    $pdo->commit();
    flash('¡Reserva creada! El guía la confirmará pronto.');
    redirect('dashboard.php');
} catch (Exception $ex) {
    $pdo->rollBack();
    flash($ex->getMessage(), 'error');
    redirect('experiencia.php?id=' . $id);
}
