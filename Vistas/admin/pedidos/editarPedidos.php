<?php 
/**
 * VISTA: /Vistas/admin/pedidos/editarPedidos.php
 * REQUERIDA: $pedido (detalle completo), $error
 */
include __DIR__ . '/../dashboard_menu.php'; 

$estados = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Pedido #<?php echo htmlspecialchars($pedido['id']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-pedidos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-info-circle"></i> Detalle Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h1>

        <div class="card resumen-pedido-header">
            <h3>Resumen del Cliente</h3>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($pedido['nombre_cliente']); ?> (<?php echo htmlspecialchars($pedido['email']); ?>)</p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($pedido['fecha_pedido']); ?></p>
            <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($pedido['metodo_pago']); ?></p>
            <p><strong>Dirección:</strong> <?php echo nl2br(htmlspecialchars($pedido['direccion'] ?? 'No especificada')); ?></p>
        </div>

        <h2>Actualizar Estado</h2>
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=pedido&a=editarpedidos&id=<?php echo $pedido['id']; ?>" class="form-checkout">
            <div class="form-group-inline">
                <label for="estado">Estado Actual:</label>
                <select name="estado" id="estado" required>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?php echo $estado; ?>" 
                            <?php echo ($pedido['estado'] === $estado) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($estado); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fa fa-sync-alt"></i> Actualizar Estado</button>
            </div>
        </form>

        <h2 class="mt-4">Productos en el Pedido</h2>
        <?php if (!empty($pedido['items'])): ?>
        <table class="tabla-data">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (Venta)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_detalle = 0; foreach ($pedido['items'] as $item): 
                    $subtotal = $item['precio_unitario'] * $item['cantidad'];
                    $total_detalle += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                    <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                    <td>S/. <?php echo number_format($item['precio_unitario'], 2); ?></td>
                    <td>S/. <?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right">TOTAL DEL PEDIDO:</td>
                    <td>S/. <?php echo number_format($total_detalle, 2); ?></td>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
            <p>Este pedido no contiene ítems.</p>
        <?php endif; ?>
        
        <p class="mt-4"><a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=verpedidos" class="btn btn-secondary">Volver al Listado</a></p>
    </div>
</body>
</html>