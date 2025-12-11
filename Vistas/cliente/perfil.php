<?php
/**
 * VISTA: /Vistas/cliente/perfil.php
 * CORREGIDO: Se eliminó la sección de Pedidos para mantener el enfoque solo en el Perfil del usuario.
 * REQUERIDA: $cliente (datos del usuario), $mensaje, $error
 */
include 'menu_cliente.php'; 

// SIMULACIÓN: Obtener tarjeta guardada desde la sesión
$tarjeta_guardada_perfil = $_SESSION['tarjeta_guardada'] ?? null;
$error = $error ?? $_GET['error'] ?? null; // Asegurar que $error se pase si existe
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
    <style>
        /* Mantener estilos de layout para tarjeta aquí */
        .form-perfil-tarjeta .group-inline {
            display: flex;
            gap: 20px;
        }
        .form-perfil-tarjeta .group-inline > div {
            flex: 1;
        }
        .form-section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #fff; /* Fondo blanco para las secciones */
        }
        .info-badge {
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 500;
            color: #666;
            margin-left: 10px;
        }
    </style>
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
                <?php if (!empty($error)): ?> 
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div> 
                <?php endif; ?>

                <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=actualizarperfil" class="form-perfil" novalidate>
                    
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
                    
                    
                    <div class="form-section form-perfil-tarjeta">
                        <h3><i class="fas fa-credit-card"></i> Gestión de Tarjeta (Solo Pruebas)</h3>
                        
                        <?php if ($tarjeta_guardada_perfil): ?>
                            <div class="alert alert-info">
                                Tienes una tarjeta registrada que termina en **<?php echo htmlspecialchars($tarjeta_guardada_perfil['ultimos_digitos']); ?>** (Exp: <?php echo htmlspecialchars($tarjeta_guardada_perfil['expiracion']); ?>). Rellena los campos para actualizarla.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No tienes una tarjeta registrada. Rellena los campos si deseas guardar una para futuros pagos.
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="num_tarjeta_perfil">Número de Tarjeta (Para Guardar/Actualizar):</label>
                            <input type="text" id="num_tarjeta_perfil" name="num_tarjeta_perfil" 
                                    placeholder="XXXX XXXX XXXX XXXX" 
                                    pattern="\d{13,16}" 
                                    title="Ingrese entre 13 y 16 dígitos."
                                    maxlength="16">
                        </div>

                        <div class="form-group group-inline">
                            <div>
                                <label for="expiracion_perfil">Expiración (MM/AA):</label>
                                <input type="text" id="expiracion_perfil" name="expiracion_perfil" 
                                        placeholder="MM/AA" 
                                        pattern="\d{2}\/\d{2}"
                                        title="Formato: MM/AA (Ej: 12/26)"
                                        maxlength="5">
                            </div>
                            <div>
                                <small class="text-muted d-block mt-4">
                                    <i class="fas fa-exclamation-triangle"></i> El CVC/CVV no se guarda por seguridad.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <button type="submit" class="btn btn-primary" title="Guardar cambios en tu perfil y tarjeta">
                            <i class="fas fa-save"></i> Guardar perfil y tarjeta
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

        <div class="profile-card mt-4">
            <div class="profile-card-body text-center">
                <h2><i class="fas fa-history"></i> Historial y Mis Pedidos</h2>
                <p>Consulta tus pedidos y su estado de envío.</p>
                <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=misPedidos" class="btn btn-info btn-lg mt-3">
                    <i class="fas fa-shopping-bag"></i> Ver Mis Pedidos
                </a>
            </div>
        </div>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.form-perfil');
            // ... (Lógica de validación de JavaScript se mantiene y es correcta) ...
            if (form) {
                form.addEventListener('submit', function(e) {
                    const nombre = document.getElementById('nombre');
                    if (nombre.value.trim().length < 3) {
                        e.preventDefault();
                        alert('El nombre debe tener al menos 3 caracteres');
                        nombre.focus();
                        return;
                    }
                    
                    const numTarjeta = document.getElementById('num_tarjeta_perfil');
                    const expiracion = document.getElementById('expiracion_perfil');
                    
                    if (numTarjeta.value && !expiracion.value) {
                         e.preventDefault();
                         alert('Si ingresas el número de tarjeta, debes ingresar la fecha de expiración.');
                         expiracion.focus();
                         return;
                    }
                    if (!numTarjeta.value && expiracion.value) {
                         e.preventDefault();
                         alert('Si ingresas la fecha de expiración, debes ingresar el número de tarjeta.');
                         numTarjeta.focus();
                         return;
                    }
                });
            }
        });
    </script>
</body>
</html>