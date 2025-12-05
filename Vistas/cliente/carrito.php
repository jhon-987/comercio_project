<?php
// VISTA: /Vistas/cliente/carrito.php
include 'menu_cliente.php'; 

$carrito = $carrito ?? [];
$total_carrito = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-carrito.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container main-content">
    <h1><i class="fa fa-shopping-cart"></i> Carrito de Compras</h1>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($carrito)): ?>
        <p class="alert alert-info">
            Tu carrito está vacío. <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo">Volver al catálogo</a>
        </p>
    <?php else: ?>
        <div class="carrito-wrapper">

            <!-- TABLA DE PRODUCTOS -->
            <div class="tabla-carrito-wrapper">
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $key => $item):
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_carrito += $subtotal;
                        ?>
                        <tr>
                            <td class="producto-nombre"><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td class="precio">S/. <?php echo number_format($item['precio'], 2); ?></td>
                            <td class="cantidad"><?php echo htmlspecialchars($item['cantidad']); ?></td>
                            <td class="subtotal">S/. <?php echo number_format($subtotal, 2); ?></td>
                            <td class="acciones">
                                <a href="<?php echo BASE_URL; ?>index.php?c=carrito&a=eliminaritem&id=<?php echo htmlspecialchars($key); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Eliminar este producto?');">
                                    <i class="fa fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right total-label">TOTAL FINAL:</td>
                            <td colspan="2" class="total-valor">S/. <?php echo number_format($total_carrito, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <hr>

            <!-- FORMULARIO DE ENVÍO Y PAGO -->
            <h2>Datos de Envío y Pago</h2>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=confirmarPedido" class="form-checkout">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección de Envío:</label>
                    <textarea id="direccion" name="direccion" placeholder="Calle, número, distrito, ciudad" required></textarea>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Pago en Efectivo (Contra Entrega)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-check-circle"></i> Confirmar Pedido
                </button>
            </form>

        </div>
    <?php endif; ?>
</div>
</body>
</html>
