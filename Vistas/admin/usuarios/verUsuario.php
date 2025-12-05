<?php 
/**
 * VISTA: /Vistas/admin/usuarios/verUsuarios.php
 * REQUERIDA: $usuarios (lista), $mensaje, $error
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container main-admin-content">
    <h1><i class="fa fa-users"></i> Gestión de Usuarios</h1>

    <?php if (!empty($mensaje)): ?> 
        <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div> 
    <?php endif; ?>
    <?php if (!empty($error)): ?> 
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> 
    <?php endif; ?>

    <p>
        <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Volver al Dashboard
        </a>
        <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=crearusuario" class="btn-crear" style="margin-left:12px;">
            <i class="fa fa-plus"></i> Crear Usuario
        </a>
    </p>

    <?php if (!empty($usuarios) && is_array($usuarios)): ?>
    <div class="table-wrapper">
        <table class="tabla-usuarios" aria-describedby="lista-usuarios">
            <colgroup>
                <col class="col-id">
                <col class="col-nombre">
                <col class="col-email">
                <col class="col-rol">
                <col class="col-telefono">
                <col class="col-registro">
                <col class="col-acciones">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Email</th>
                    <th scope="col">Rol</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Registro</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $usuario): 
                $id = isset($usuario['id']) ? (int)$usuario['id'] : 0;
                $nombre = $usuario['nombre'] ?? '';
                $email = $usuario['email'] ?? '';
                $rol = $usuario['rol'] ?? 'cliente';
                $telefono = $usuario['telefono'] ?? 'N/A';
                $fecha = $usuario['fecha_registro'] ?? ($usuario['created_at'] ?? null);
                $fecha_corto = $fecha ? date('d/m/Y', strtotime($fecha)) : '-';
                $subline = $usuario['username'] ?? $email;
            ?>
                <tr>
                    <td data-label="ID"><?php echo $id; ?></td>

                    <td class="nombre" data-label="Nombre">
                        <div class="nombre-con-mini">
                            <div class="usuario-nombre"><?php echo htmlspecialchars($nombre); ?></div>
                            <div class="usuario-email"><?php echo htmlspecialchars($subline); ?></div>
                        </div>
                    </td>

                    <td class="email" data-label="Email" title="<?php echo htmlspecialchars($email); ?>">
                        <?php echo htmlspecialchars($email); ?>
                    </td>

                    <td data-label="Rol">
                        <span class="rol rol-<?php echo htmlspecialchars($rol); ?>">
                            <?php echo htmlspecialchars(ucfirst($rol)); ?>
                        </span>
                    </td>

                    <td class="usuario-telefono" data-label="Teléfono"><?php echo htmlspecialchars($telefono); ?></td>

                    <td data-label="Registro"><?php echo htmlspecialchars($fecha_corto); ?></td>

                    <td data-label="Acciones">
                        <div class="usuario-acciones">
                            <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=editarusuario&id=<?php echo $id; ?>" class="btn btn-warning">Editar</a>
                            <a href="<?php echo BASE_URL; ?>index.php?c=usuario&a=verusuarios&eliminar_id=<?php echo $id; ?>" 
                               onclick="return confirm('¿Eliminar usuario #<?php echo $id; ?>? Esta acción es irreversible.');" 
                               class="btn btn-danger">Eliminar</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="empty-state" style="margin-top:20px;">
            <h2>No hay usuarios registrados</h2>
            <p>Registra nuevos usuarios desde el botón Crear.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
