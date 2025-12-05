<?php
// VISTA: /Vistas/auth/login.php
include __DIR__ . '/../cliente/menu_cliente.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/auth-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <h2>Iniciar Sesión</h2>
        <p class="subtitle">Ingresa tus credenciales para continuar</p>

        <?php 
        $msg = $error ?? $mensaje ?? null;
        if (isset($_GET['m']) && !empty($_GET['m'])) {
            $msg = htmlspecialchars($_GET['m']);
            echo '<p class="alert alert-success">' . $msg . '</p>';
        } else if ($msg) {
            echo '<p class="alert alert-danger">' . htmlspecialchars($msg) . '</p>';
        }
        ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=auth&a=login">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="tu.correo@ejemplo.com">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="Contraseña">
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        
        <p class="login-footer">
            ¿No tienes cuenta? 
            <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=registro">Regístrate aquí</a>
        </p>
    </div>
</div>
</body>
</html>
