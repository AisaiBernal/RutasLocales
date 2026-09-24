<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_experiencia = $_POST['id_experiencia'];
    $id_turista = $_POST['id_turista'];
    $cantidad = $_POST['cantidad'];
    $fecha = $_POST['fecha'];

    if (!empty($id_experiencia) && !empty($id_turista) && $cantidad > 0 && !empty($fecha)) {
        $sql = "INSERT INTO reservas (id_turista, id_experiencia, cantidad_personas, fecha_reserva) 
                VALUES (:turista, :experiencia, :cantidad, :fecha)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([
                ':turista' => $id_turista,
                ':experiencia' => $id_experiencia,
                ':cantidad' => $cantidad,
                ':fecha' => $fecha
            ]);
            header("Location: dashboard.php?mensaje=reserva_exitosa");
            exit;
        } catch (PDOException $e) {
            echo "Error al guardar la reserva: " . $e->getMessage();
        }
    } else {
        echo "Por favor, completa todos los campos correctamente.";
    }
}
?>