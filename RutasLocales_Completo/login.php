<?php
session_start();
require 'conexion.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - RutasLocales</title>
    <!-- FontAwesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Enlace a tu archivo CSS principal -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <div class="overlay"></div>

    <div class="login-modal">
        <div class="modal-header">
            <h2>Iniciar Sesión</h2>
            <!-- Botón X para regresar al index.php -->
            <a href="index.php" class="close-btn"><i class="fa-solid fa-xmark"></i></a>
        </div>
        
        <div class="modal-body">
            <p class="subtitle">Todos los campos son obligatorios.</p>
            
            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="input-group">
                    <label>Correo electrónico</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="input-group">
                    <label>Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" required>
                        <button type="button" class="toggle-password" id="toggleBtn">
                            <i class="fa-regular fa-eye"></i> mostrar
                        </button>
                    </div>
                </div>

                <a href="#" class="forgot-link">¿Olvidaste tu información?</a>
                
                <button type="submit" class="btn-primary">Iniciar Sesión</button>
            </form>
        </div>

        <div class="modal-footer">
            <p>¿No estás registrado en RutasLocales?</p>
            <a href="registro.php" class="btn-secondary">Regístrate ahora</a>
            <p class="secure-info">Tu información está segura <i class="fa-solid fa-lock"></i></p>
        </div>
    </div>

    <!-- Script para revelar la contraseña -->
    <script>
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