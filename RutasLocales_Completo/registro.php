<?php
require 'conexion.php';
$mensaje = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        if($stmt->execute([$nombre, $email, $password, $rol])) {
            $mensaje = "Cuenta creada exitosamente. Ya puedes iniciar sesión.";
        }
    } catch (PDOException $e) {
        $error = "Error al registrar. Es posible que el correo ya esté en uso.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - RutasLocales</title>
    <!-- FontAwesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Enlace a tu archivo CSS principal -->
    <link rel="stylesheet" href="style.css">
</head>
<!-- Reutilizamos la clase login-page para mantener el mismo fondo y centrado -->
<body class="login-page">

    <div class="overlay"></div>

    <div class="login-modal">
        <div class="modal-header">
            <h2>Crear Cuenta</h2>
            <!-- Botón X para regresar al index.php -->
            <a href="index.php" class="close-btn"><i class="fa-solid fa-xmark"></i></a>
        </div>
        
        <div class="modal-body">
            <p class="subtitle">Únete a RutasLocales. Todos los campos son obligatorios.</p>
            
            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if($mensaje): ?>
                <div class="success-msg"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <form id="formRegistro" method="POST" action="registro.php">
                <div class="input-group">
                    <label>Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="input-group">
                    <label>Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="input-group">
                    <label>Contraseña (Mín. 6 caracteres)</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>
                        <button type="button" class="toggle-password" id="toggleBtn">
                            <i class="fa-regular fa-eye"></i> mostrar
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <label>¿Qué buscas en RutasLocales?</label>
                    <select name="rol" required>
                        <option value="turista">Soy Turista (Quiero explorar planes)</option>
                        <option value="guia">Soy Guía (Quiero ofrecer mis tours)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-primary" style="margin-top: 10px;">Completar Registro</button>
            </form>
        </div>

        <div class="modal-footer">
            <p>¿Ya tienes una cuenta?</p>
            <a href="login.php" class="btn-secondary">Inicia Sesión aquí</a>
            <p class="secure-info">Registro seguro y encriptado <i class="fa-solid fa-shield-halved"></i></p>
        </div>
    </div>

    <!-- Script para validación y revelar la contraseña -->
    <script>
        // Validar contraseña antes de enviar
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            let pass = document.getElementById('password').value;
            if(pass.length < 6) {
                e.preventDefault();
                alert("La contraseña debe tener al menos 6 caracteres por seguridad.");
            }
        });

        // Revelar/Ocultar contraseña
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggleBtn');

        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fa-regular fa-eye-slash"></i> ocultar';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fa-regular fa-eye"></i> mostrar';
            }
        });
    </script>
</body>
</html>