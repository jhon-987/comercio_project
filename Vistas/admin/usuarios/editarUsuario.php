<?php 
/**
 * VISTA: /Vistas/admin/usuarios/editarUsuario.php
 * REQUERIDA: $usuario (datos del usuario), $error
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario #<?php echo htmlspecialchars($usuario['id'] ?? 'N/A'); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-edit"></i> Editar Usuario #<?php echo htmlspecialchars($usuario['id'] ?? 'N/A'); ?></h1>
        
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=usuario&a=editarusuario&id=<?php echo $usuario['id'] ?? ''; ?>" class="form-perfil">
            
            <div class="form-group">
                <label for="nombre">Nombre completo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" disabled title="El correo no es editable.">
            </div>
            
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="cliente" <?php echo ($usuario['rol'] === 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                    <option value="empleado" <?php echo ($usuario['rol'] === 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                    <option value="admin" <?php echo ($usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar Cambios</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=verusuarios" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</body>
</html>