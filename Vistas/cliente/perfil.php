<?php 
/**
 * VISTA: /Vistas/cliente/perfil.php
 * REQUERIDA: $cliente (datos del usuario), $mensaje
 */
include 'menu_cliente.php'; 
// Asegúrate de que $cliente es un array con 'nombre', 'correo', 'telefono', 'direccion'
// $cliente es pasado por ClienteController::perfil()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Cliente | Tu Tienda Online</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container main-content">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>
        </div>

        <div class="profile-card">
            <div class="profile-card-header">
                <h2>¡Hola, <?php echo htmlspecialchars(explode(' ', $cliente['nombre'] ?? '')[0]); ?>!</h2>
                <p>Tu información personal y detalles de cuenta</p>
            </div>
            
            <div class="profile-card-body">
                <?php if (!empty($mensaje)): ?> 
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div> 
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=actualizarperfil" class="form-perfil" novalidate>
                    
                    <!-- Sección de Información Personal -->
                    <div class="form-section">
                        <h3><i class="fas fa-user"></i> Información Personal</h3>
                        
                        <div class="form-group">
                            <label for="nombre">
                                <i class="fas fa-signature"></i> Nombre completo:
                            </label>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>" 
                                   required minlength="3" maxlength="100">
                        </div>
                        
                        <div class="form-group">
                            <label for="correo">
                                <i class="fas fa-envelope"></i> Correo electrónico:
                                <span class="info-badge">No editable</span>
                            </label>
                            <input type="email" id="correo" 
                                   value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" 
                                   disabled title="El correo no es editable por seguridad.">
                        </div>
                        
                        <div class="form-group">
                            <label for="telefono">
                                <i class="fas fa-phone"></i> Teléfono:
                            </label>
                            <input type="tel" id="telefono" name="telefono" 
                                   value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>"
                                   placeholder="Ej: +52 55 1234 5678" maxlength="20">
                        </div>
                    </div>

                    <!-- Sección de Dirección -->
                    <div class="form-section">
                        <h3><i class="fas fa-map-marker-alt"></i> Dirección</h3>
                        
                        <div class="form-group">
                            <label for="direccion">
                                <i class="fas fa-home"></i> Dirección completa:
                            </label>
                            <textarea id="direccion" name="direccion" 
                                      placeholder="Tu dirección completa (calle, número, colonia, ciudad, estado, código postal)"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="actions">
                        <button type="submit" class="btn btn-primary" title="Guardar cambios en tu perfil">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                        <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=cambiarpassword" 
                           class="btn btn-secondary" 
                           title="Cambiar tu contraseña">
                            <i class="fas fa-lock"></i> Cambiar contraseña
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección de Pedidos -->
        <div class="profile-card">
            <div class="profile-card-body">
                <div class="orders-section">
                    <h2><i class="fas fa-shopping-bag"></i> Mis Pedidos Anteriores</h2>
                    <div class="orders-placeholder">
                        <i class="fas fa-box-open fa-2x mb-3"></i>
                        <p class="mb-2">¡Descubre nuestras colecciones exclusivas y disfruta de envíos rápidos y seguros!</p>
                        <p>¿Listo para tu primera experiencia de compra? <a href="<?php echo BASE_URL; ?>index.php?c=tienda" class="text-primary font-weight-bold">Explora nuestra tienda ahora</a></p>
                    </div>
                    
                    <!-- Placeholder para futura implementación -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="fas fa-star text-warning"></i> <strong>¡Próximamente!</strong> Panel completo de seguimiento con estados en tiempo real, historial detallado y opciones de devolución simplificadas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para validación mejorada -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.form-perfil');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nombre = document.getElementById('nombre');
                    if (nombre.value.trim().length < 3) {
                        e.preventDefault();
                        alert('El nombre debe tener al menos 3 caracteres');
                        nombre.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>