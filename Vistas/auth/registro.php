<?php
/**
 * VISTA: /Vistas/auth/registro.php
 * Propósito: Muestra el formulario de registro.
 */
// Incluye el menú del cliente
include __DIR__ . '/../cliente/menu_cliente.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/auth-registro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="registro-wrapper">
    <div class="registro-card">
        <h2>Crear Cuenta</h2>
        <p class="subtitle">Completa tus datos para registrarte</p>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($mensaje) && $mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=auth&a=registro">
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña (Mínimo 6 caracteres):</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="admin_code">Código de Empleado (Opcional):</label>
                <input type="text" id="admin_code" name="admin_code" placeholder="Código de autorización">
            </div>
            
            <button type="submit" class="btn-registro">Registrarme</button>
        </form>
        
        <p class="registro-footer">
            ¿Ya tienes cuenta? 
            <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=login">Inicia Sesión</a>
        </p>
    </div>
</div>
</body>
</html>
