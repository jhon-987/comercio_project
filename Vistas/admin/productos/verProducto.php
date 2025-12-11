<?php 
/**
 * VISTA: /Vistas/admin/productos/verProducto.php
 * REQUERIDA: $productos (lista), $mensaje, $error
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">

        <h1><i class="fa fa-cubes"></i> Listado de Productos</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <p>
            <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=crear" class="btn btn-success">
                <i class="fa fa-plus" aria-hidden="true"></i> <span class="btn-text">Añadir Nuevo Producto</span>
            </a>
            <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="btn btn-secondary">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Volver
            </a>
        </p>

        <?php if (!empty($productos) && is_array($productos)): ?>
            <div class="table-wrapper">
            <table class="tabla-productos" aria-describedby="lista-productos">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Categoría</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($productos as $producto): 
                        $id = $producto['id'] ?? 0;
                        // Usa 'imagen_url' o 'imagen' dependiendo de tu BD
                        $imagen_src = $producto['imagen_url'] ?? $producto['imagen'] ?? 'default.jpg'; 
                        $nombre = $producto['nombre'] ?? '-';
                        $precio = number_format($producto['precio'] ?? 0, 2);
                        $stock = $producto['stock'] ?? '0';
                        $categoria_nombre = $producto['categoria_nombre'] ?? 'Sin categoría';
                    ?>
                    <tr>
                        <td class="td-id" data-label="ID"><?php echo $id; ?></td>

                        <td class="td-nombre" data-label="Nombre">
                            <div class="nombre-con-mini">
                                <img class="prod-miniatura" src="<?php echo BASE_URL . 'public/img/' . htmlspecialchars($imagen_src); ?>" alt="Imagen <?php echo htmlspecialchars($nombre); ?>">
                                <div class="prod-nombre"><?php echo htmlspecialchars($nombre); ?></div>
                            </div>
                        </td>
                        
                        <td><?php echo htmlspecialchars($categoria_nombre); ?></td>

                        <td class="td-precio nowrap" data-label="Precio">S/. <?php echo $precio; ?></td>

                        <td class="td-stock nowrap" data-label="Stock"><?php echo htmlspecialchars($stock); ?></td>

                        <td class="td-acciones" data-label="Acciones">
                            <div class="producto-acciones" role="group" aria-label="Acciones producto <?php echo $id; ?>">
                                <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=editar&id=<?php echo $id; ?>" 
                                   class="btn btn-warning btn-sm" title="Editar producto <?php echo $id; ?>">
                                    <i class="fa fa-edit" aria-hidden="true"></i> Editar
                                </a>

                                <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=eliminar&id=<?php echo $id; ?>"
                                   onclick="return confirm('¿Eliminar producto? Esta acción no se puede deshacer.');"
                                   class="btn btn-danger btn-sm" title="Eliminar producto <?php echo $id; ?>">
                                    <i class="fa fa-trash" aria-hidden="true"></i> Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h2>No hay productos registrados</h2>
                <p>Agrega nuevos productos desde el botón <strong>Añadir Nuevo Producto</strong>.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>