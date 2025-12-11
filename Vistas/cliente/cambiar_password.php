<?php
/**
 * VISTA: /Vistas/cliente/cambiar_password.php
 */
include 'menu_cliente.php'; 

// Las variables $mensaje y $error se obtienen en ClienteController::cambiarpassword()
$mensaje = $_GET['mensaje'] ?? null;
$error = $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-perfil { max-width: 500px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #fff; }
    </style>
</head>
<body>
<div class="container main-content">
    <h1><i class="fa fa-lock"></i> Cambiar Contraseña</h1>

    <?php if (!empty($mensaje)): ?> 
        <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div> 
    <?php endif; ?>
    <?php if (!empty($error)): ?> 
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> 
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=actualizarPassword" class="form-perfil">
        
        <p class="text-info">Por seguridad, debes confirmar tu contraseña actual antes de establecer una nueva.</p>
        
        <div class="form-group">
            <label for="actual_password">Contraseña Actual:</label>
            <input type="password" id="actual_password" name="actual_password" required>
        </div>
        
        <hr>

        <div class="form-group">
            <label for="nueva_password">Nueva Contraseña:</label>
            <input type="password" id="nueva_password" name="nueva_password" required minlength="6">
        </div>
        
        <div class="form-group">
            <label for="confirmacion_password">Confirmar Nueva Contraseña:</label>
            <input type="password" id="confirmacion_password" name="confirmacion_password" required minlength="6">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Guardar Nueva Contraseña
        </button>
        <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=perfil" class="btn btn-secondary">Volver al Perfil</a>
    </form>
</div>
</body>
</html>