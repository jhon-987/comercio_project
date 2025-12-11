<?php
/**
 * VISTA: /Vistas/admin/pedidos/verPedidos.php
 * REQUERIDA: $pedidos (lista de pedidos), $mensaje, $error
 * NOTA: Esta vista asume que el CONTROLADOR ya cargó el menú y los datos.
 */
// 🛑 ÚNICA INCLUSIÓN PERMITIDA: Incluir el layout de navegación (el menú admin).
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-clipboard-list"></i> Listado de Pedidos</h1>
        
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=crearpedidos" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Crear Pedido Manual
            </a>
        </div>

        <?php if (!empty($pedidos) && is_array($pedidos)): ?>
        <table class="tabla-data">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($pedido['id']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['nombre_usuario'] ?? $pedido['nombre_cliente'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                    <td>S/. <?php echo number_format($pedido['total'], 2); ?></td>
                    <td>
                        <span class="badge badge-<?php 
                            // Asumiendo clases CSS para estados
                            if ($pedido['estado'] === 'entregado') echo 'success';
                            else if ($pedido['estado'] === 'cancelado') echo 'danger';
                            else if ($pedido['estado'] === 'procesando') echo 'warning';
                            else echo 'info';
                        ?>">
                            <?php echo ucfirst(htmlspecialchars($pedido['estado'])); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=editarpedidos&id=<?php echo htmlspecialchars($pedido['id']); ?>" class="btn btn-sm btn-info" title="Ver/Editar Detalle">
                            <i class="fa fa-eye"></i> Detalle
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=verpedidos&eliminar_id=<?php echo htmlspecialchars($pedido['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este pedido?');" title="Eliminar Pedido">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info">No se encontraron pedidos en el sistema.</div>
        <?php endif; ?>
    </div>
</body>
</html>