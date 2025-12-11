<?php 
/**
 * VISTA: /Vistas/cliente/mis_pedidos.php
 * REQUERIDA: $pedidos (lista de pedidos del usuario logueado)
 */
include 'menu_cliente.php'; 
// La variable $pedidos debe ser pasada por ClienteController->misPedidos()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Historial de Pedidos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .badge-pendiente { background-color: #ffc107; color: black; padding: 4px 8px; border-radius: 4px; }
        .badge-procesando { background-color: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; }
        .badge-enviado { background-color: #007bff; color: white; padding: 4px 8px; border-radius: 4px; }
        .badge-entregado { background-color: #28a745; color: white; padding: 4px 8px; border-radius: 4px; }
        .badge-cancelado { background-color: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container main-content">
        <h1><i class="fas fa-history"></i> Historial y Seguimiento de Pedidos</h1>

        <?php if (empty($pedidos) || !is_array($pedidos)): ?>
            <p class="alert alert-info">Aún no tienes pedidos registrados. ¡Es hora de comprar!</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th># Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): 
                        $pedido_id = $pedido['id'] ?? 0;
                        $estado = strtolower($pedido['estado'] ?? 'pendiente');
                        $clase_badge = 'badge-' . $estado;
                    ?>
                        <tr>
                            <td>PED-<?php echo htmlspecialchars($pedido_id); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime(htmlspecialchars($pedido['fecha_pedido']))); ?></td>
                            <td>S/. <?php echo number_format($pedido['total'] ?? 0, 2); ?></td>
                            <td>
                                <span class="badge <?php echo $clase_badge; ?>"><?php echo htmlspecialchars(ucfirst($estado)); ?></span>
                            </td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=descargarFactura&id=<?php echo $pedido_id; ?>" class="btn btn-sm btn-secondary" target="_blank" title="Descargar Guía/Factura">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>