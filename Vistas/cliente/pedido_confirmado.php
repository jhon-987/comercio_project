<?php
/**
 * VISTA: /Vistas/cliente/pedido_confirmado.php
 * REQUERIDA: $codigoPedido, $total, $nombreCliente, $direccion, $metodoPago (pasados por el controlador)
 */
include 'menu_cliente.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido Confirmado</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-pedido-confirmado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-content">
        <h1><i class="fa fa-gift"></i> ¡Pedido Confirmado!</h1>
        
        <div class="alert alert-success">
            <p>Tu pedido **#<?php echo htmlspecialchars($codigoPedido ?? 'N/A'); ?>** ha sido registrado exitosamente.</p>
            <p>Gracias por tu compra. Te notificaremos cuando esté en camino.</p>
        </div>

        <div class="card resumen-pedido">
            <h2>Detalles del Pago</h2>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($nombreCliente ?? 'N/A'); ?></p>
            <p><strong>Total Final:</strong> S/. <?php echo number_format($total ?? 0, 2); ?></p>
            <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($metodoPago ?? 'N/A'); ?></p>
            <p><strong>Dirección de Envío:</strong> <?php echo nl2br(htmlspecialchars($direccion ?? 'N/A')); ?></p>
        </div>
        
        <p class="mt-4"><a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo" class="btn btn-secondary">Continuar Comprando</a></p>
    </div>
</body>
</html>