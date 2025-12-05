<?php 
/**
 * VISTA: /Vistas/admin/pedidos/verPedidos.php
 * REQUERIDA: $pedidos (lista), $mensaje, $error
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-pedidos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-clipboard-list"></i> Listado de Pedidos</h1>
        
        <?php if (!empty($mensaje)): ?> <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div> <?php endif; ?>
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> <?php endif; ?>

        <p><a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Volver al Dashboard</a></p>
        <p><a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=crearpedidos" class="btn btn-success"><i class="fa fa-plus"></i> Crear Pedido Manual</a></p>

        <?php if (!empty($pedidos)): ?>
            <table class="tabla-data">
                <thead>
                    <tr>
                        <th>ID</th>
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
                            <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                            <td><?php echo htmlspecialchars($pedido['nombre_cliente'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['fecha_pedido']); ?></td>
                            <td>S/. <?php echo number_format($pedido['total'], 2); ?></td>
                            <td><span class="estado estado-<?php echo htmlspecialchars($pedido['estado']); ?>"><?php echo htmlspecialchars($pedido['estado']); ?></span></td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=editarpedidos&id=<?php echo $pedido['id']; ?>" class="btn btn-info btn-sm">Ver/Editar</a>
                                <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=verpedidos&eliminar_id=<?php echo $pedido['id']; ?>" 
                                   onclick="return confirm('¿Está seguro de eliminar el pedido completo?');" 
                                   class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay pedidos registrados.</p>
        <?php endif; ?>
    </div>
</body>
</html>