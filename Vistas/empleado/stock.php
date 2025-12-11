<?php
/**
 * VISTA: /Vistas/empleado/stock.php
 * REQUERIDA: $productos (lista, cargada por EmpleadoController)
 */
include __DIR__ . '/../admin/dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Stock (Empleado)</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/empleado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-warehouse"></i> Revisión de Stock</h1>
        
        <p class="lead">Puede modificar el inventario.</p>

        <?php $productos = $productos ?? []; ?>
        <?php if (!empty($productos)): ?>
            <table class="tabla-data">
                </table>
        <?php else: ?>
            <p>No hay productos en inventario.</p>
        <?php endif; ?>
    </div>
</body>
</html>