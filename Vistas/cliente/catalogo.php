<?php 
// VISTA: /Vistas/cliente/catalogo.php
include 'menu_cliente.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Productos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-catalogo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
</head>
<body>
    <div class="container main-content">
        <h1>Catálogo de productos</h1>
        
        <?php if (empty($productos) || !is_array($productos)): ?>
            <p class="alert alert-info">No hay productos disponibles en este momento.</p>
        <?php else: ?>
            
            <div class="product-grid">
                <?php foreach ($productos as $producto): ?>
                    <div class="card producto-card">
                        <div class="producto-detalle">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p class="precio">S/. <?php echo number_format($producto['precio'], 2); ?></p>

                            <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=agregarCarrito">
                                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id']); ?>">
                                <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>">

                                <div class="form-group-inline">
                                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo htmlspecialchars($producto['stock'] ?? '1'); ?>" required class="input-cantidad">
                                    
                                    <?php if (($producto['stock'] ?? 0) > 0): ?>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-cart-plus"></i> Agregar
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-danger btn-sm" disabled>
                                            Agotado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</body>
</html>