<?php
/**
 * VISTA: /Vistas/admin/dashboard.php
 * REQUERIDA: $titulo (del AdminController)
 */
include 'dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo ?? 'Administración'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container main-admin-content">
        <h1><?php echo $titulo ?? 'Dashboard'; ?></h1>
        <p class="lead">Bienvenido al panel de control de tu comercio electrónico.</p>
        
        <div class="admin-menu-grid">
            
            <div class="card card-admin">
                <h2><i class="fa fa-cubes"></i> Productos</h2>
                <p>Gestión completa del catálogo.</p>
                <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=verproducto" class="btn btn-primary">Gestionar Productos</a>
            </div>

            <div class="card card-admin">
                <h2><i class="fa fa-clipboard-list"></i> Pedidos</h2>
                <p>Revisión y actualización de estados de órdenes.</p>
                <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=verpedidos" class="btn btn-warning">Gestionar Pedidos</a>
            </div>

            <div class="card card-admin">
                <h2><i class="fa fa-users"></i> Usuarios</h2>
                <p>Ver y gestionar cuentas de clientes.</p>
                <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=verusuarios" class="btn btn-info">Gestionar Usuarios</a>
            </div>
            
            <div class="card card-admin">
                <h2><i class="fa fa-shopping-bag"></i> Ver Tienda</h2>
                <p>Volver a la vista del cliente (Catálogo).</p>
                <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo" class="btn btn-secondary">Ir a Tienda</a>
            </div>

        </div>
    </div>
</body>
</html>