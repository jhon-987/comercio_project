<?php
/**
 * VISTA: /Vistas/admin/productos/crearProducto.php
 * REQUERIDA: $error
 */
include __DIR__ . '/../dashboard_menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-plus"></i> Crear Nuevo Producto</h1>
        
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=producto&a=crearproducto" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio (S/.):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">
            </div>
            
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Producto</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=verproducto" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>