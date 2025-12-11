<?php
/**
 * VISTA: /Vistas/empleado/pedidos.php
 * REQUERIDA: $pedidos (lista, cargada por EmpleadoController)
 */
include __DIR__ . '/../admin/dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos Pendientes (Empleado)</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/empleado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-clipboard-check"></i> Pedidos (Vista Empleado)</h1>
        
        <p class="lead">Solo tiene permisos para ver y actualizar pedidos pendientes o en proceso.</p>

        <?php $pedidos = $pedidos ?? []; ?>
        <?php if (!empty($pedidos)): ?>
            <table class="tabla-data">
                </table>
        <?php else: ?>
            <p>No hay pedidos pendientes asignados.</p>
        <?php endif; ?>
    </div>
</body>
</html>