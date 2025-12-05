<?php
/**
 * VISTA: /Vistas/cliente/menu_cliente.php
 */
$logueado = isset($_SESSION['usuario_id']);
$es_admin = $logueado && isset($_SESSION['rol']) && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'empleado');
?>
<div class="container-full-width">
    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo"><i class="fa fa-th-large"></i> Catálogo</a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=carrito"><i class="fa fa-shopping-cart"></i> Carrito</a></li>
                
                <?php if ($logueado): ?>
                    <li><a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=perfil"><i class="fa fa-user"></i> Perfil</a></li>
                    
                    <?php if ($es_admin): ?>
                    <li class="btn-admin"> 
                        <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" style="color: white; font-weight: bold;">
                            <i class="fa fa-lock"></i> Gestión Admin
                        </a>
                    </li>
                    <?php endif; ?>

                    <li class="btn-right">
                        <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=logout"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
                    </li>
                    
                <?php else: ?>
                    <li class="btn-right">
                        <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=login"><i class="fa fa-sign-in-alt"></i> Iniciar Sesión</a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php?c=auth&a=registro"><i class="fa fa-user-plus"></i> Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</div>