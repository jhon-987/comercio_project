<?php
/**
 * VISTA: /Vistas/admin/productos/editarProducto.php
 * REQUERIDA: $producto (datos del producto), $error, $categorias (lista de categorías)
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

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=producto&a=editar&id=<?php echo $producto['id']; ?>" enctype="multipart/form-data">
            
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="categoria_id">Categoría:</label>
                <select id="categoria_id" name="categoria_id" required>
                    <option value="">-- Seleccione una Categoría --</option>
                    <?php 
                    // Se asume que $categorias es pasado desde ProductoController->editar()
                    if (!empty($categorias)):
                        $current_cat_id = $producto['categoria_id'] ?? null;
                        foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>"
                                <?php echo ($current_cat_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach;
                    endif;
                    ?>
                </select>
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
                <img src="<?php echo BASE_URL; ?>public/img/<?php echo htmlspecialchars($producto['imagen_url'] ?? 'default.jpg'); ?>" alt="Imagen Actual" width="100"><br>
                
                <label for="imagen_url">Cambiar Imagen URL:</label>
                <input type="text" id="imagen_url" name="imagen_url" value="<?php echo htmlspecialchars($producto['imagen_url'] ?? ''); ?>" placeholder="Ej: url_imagen/producto.jpg">
                </div>
            
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Guardar Cambios</button>
            <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=index" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>