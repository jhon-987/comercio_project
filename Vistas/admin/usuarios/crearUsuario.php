<?php 
/**
 * VISTA: /Vistas/admin/usuarios/crearUsuario.php
 * Propósito: Formulario para crear un nuevo usuario (cliente, empleado o admin).
 * REQUERIDA: $error, $mensaje
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-user-plus"></i> Crear Nuevo Usuario</h1>
        
        <?php if (!empty($mensaje)): ?> <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div> <?php endif; ?>
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=usuario&a=crearusuario">
            
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="cliente">Cliente</option>
                    <option value="empleado">Empleado</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Usuario</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=verusuarios" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>