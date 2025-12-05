<?php
/**
 * VISTA: /Vistas/admin/productos/editarProducto.php
 * REQUERIDA: $producto (datos del producto), $error
 */
include __DIR__ . '/../dashboard_menu.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto #<?php echo htmlspecialchars($producto['id']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-edit"></i> Editar Producto #<?php echo htmlspecialchars($producto['id']); ?></h1>
        
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=producto&a=editarproducto&id=<?php echo $producto['id']; ?>" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio">Precio (S/.):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Imagen Actual:</label><br>
                <img src="<?php echo BASE_URL; ?>public/img/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.jpg'); ?>" alt="Imagen Actual" width="100"><br>
                
                <label for="imagen_nueva">Cambiar Imagen:</label>
                <input type="file" id="imagen_nueva" name="imagen_nueva" accept="image/*">
                <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($producto['imagen']); ?>">
            </div>
            
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Cambios</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=verproducto" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>