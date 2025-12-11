<?php
/**
 * VISTA: /Vistas/admin/dashboard_menu.php
 * Propósito: Layout de navegación interna para TODAS las vistas de Admin.
 */
$logueado = isset($_SESSION['usuario_id']);
// 🛑 CORRECCIÓN: Usar la clave correcta para el nombre
$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Administrador';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="navbar-admin">
    <div class="container">
        <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="brand">
            <i class="fa fa-tachometer-alt"></i> Panel Admin
        </a>

        <nav class="admin-nav-links">
            <a href="<?php echo BASE_URL; ?>index.php?c=producto&a=verproducto"><i class="fa fa-cubes"></i> Productos</a>
            <a href="<?php echo BASE_URL; ?>index.php?c=pedido&a=verpedidos"><i class="fa fa-clipboard-list"></i> Pedidos</a>
            <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=verusuarios"><i class="fa fa-users"></i> Usuarios</a>
        </nav>

        <div class="user-info">
            <span>Hola, <?php echo htmlspecialchars($nombre_usuario); ?></span>
            <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=logout" class="btn btn-danger btn-sm">
                <i class="fa fa-sign-out-alt"></i> Salir
            </a>
        </div>
    </div>
</div>