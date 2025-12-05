<?php 
/**
 * VISTA: /Vistas/cliente/cambiar_password.php
 * REQUERIDA: $error, $mensaje
 */
include 'menu_cliente.php'; 
// Las variables $error y $mensaje son pasadas por ClienteController::cambiarpassword()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-cambiar-password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-content">
        <h1><i class="fa fa-key"></i> Cambiar Contraseña</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=cambiarpassword" class="form-perfil">
            
            <div class="form-group">
                <label for="current_password">Contraseña Actual:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Nueva Contraseña (Mín. 6 caracteres):</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Nueva Contraseña</button>
                <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=perfil" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>