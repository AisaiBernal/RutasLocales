<?php
session_start();
require 'conexion.php';

if(!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

if(isset($_GET['eliminar']) && $_SESSION['rol'] == 'guia') {
    $id_eliminar = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM experiencias WHERE id_experiencia = ? AND id_guia = ?");
    $stmt->execute([$id_eliminar, $_SESSION['usuario_id']]);
    header("Location: dashboard.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['rol'] == 'guia') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    
    $sql = "INSERT INTO experiencias (id_guia, titulo, descripcion, precio, ubicacion, cupos_disponibles) VALUES (?, ?, ?, ?, 'Bogotá/Cundinamarca', 10)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id'], $titulo, $descripcion, $precio]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?> (<?php echo $_SESSION['rol']; ?>)</h1>
        <a href="logout.php" style="color:white;">Cerrar Sesión</a>
    </nav>

    <div style="padding: 2rem;">
        <?php if($_SESSION['rol'] == 'guia'): ?>
            <h2>Publicar Nueva Experiencia Turística</h2>
            <form method="POST" style="max-width: 400px; margin-bottom: 2rem;">
                <input type="text" name="titulo" placeholder="Título (ej: Páramo de Sumapaz)" required>
                <textarea name="descripcion" placeholder="Descripción del recorrido..." required></textarea>
                <input type="number" name="precio" placeholder="Precio COP" required>
                <button type="submit">Guardar Experiencia</button>
            </form>

            <h2>Tus Experiencias (CRUD)</h2>
            <table border="1" width="100%" style="border-collapse: collapse; text-align: left; background: white;">
                <tr>
                    <th style="padding: 10px;">Título</th>
                    <th style="padding: 10px;">Precio</th>
                    <th style="padding: 10px;">Acciones</th>
                </tr>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM experiencias WHERE id_guia = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                while($fila = $stmt->fetch()) {
                    echo "<tr>
                            <td style='padding: 10px;'>{$fila['titulo']}</td>
                            <td style='padding: 10px;'>\${$fila['precio']}</td>
                            <td style='padding: 10px;'>
                                <a href='dashboard.php?eliminar={$fila['id_experiencia']}' onclick='return confirmarEliminar()' style='color: red;'>Eliminar</a>
                            </td>
                          </tr>";
                }
                ?>
            </table>

            <script>
                function confirmarEliminar() {
                    return confirm("¿Estás seguro de que deseas eliminar esta experiencia?");
                }
            </script>

        <?php else: ?>
            <h2>Explorar Planes</h2>
            <p>Ve a la <a href="index.php">página principal</a> para buscar y reservar experiencias.</p>
            
            <h2>Tus Reservas</h2>
            <ul>
                <?php
                $stmt = $pdo->prepare("SELECT e.titulo, r.fecha_reserva FROM reservas r JOIN experiencias e ON r.id_experiencia = e.id_experiencia WHERE r.id_turista = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                while($reserva = $stmt->fetch()) {
                    echo "<li>{$reserva['titulo']} - Reservado para el: {$reserva['fecha_reserva']}</li>";
                }
                ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>