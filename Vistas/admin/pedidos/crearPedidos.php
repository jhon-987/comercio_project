<?php 
/**
 * VISTA: /Vistas/admin/pedidos/crearPedidos.php
 * REQUERIDA: $clientes (lista de usuarios cliente), $productos_catalogo (lista de productos), $error
 */
include __DIR__ . '/../dashboard_menu.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Nuevo Pedido Manual</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/admin-pedidos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
<body>
    <div class="container main-admin-content">
        <h1><i class="fa fa-file-invoice-dollar"></i> Crear Nuevo Pedido Manual</h1>
        
        <?php if (!empty($error)): ?> 
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div> 
        <?php endif; ?>

        <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=pedido&a=crearpedidos">
            
            <div class="seccion-pedido card p-4 mb-4">
                <h2>1. Datos del Cliente</h2>
                <div class="form-group">
                    <label for="usuario_id">Cliente Asociado:</label>
                    <select id="usuario_id" name="usuario_id" required>
                        <option value="">-- Seleccione un Cliente --</option>
                        <?php 
                        // Asumimos que $clientes viene del controlador (UsuarioModelo::obtenerClientes())
                        if (!empty($clientes)): 
                            foreach ($clientes as $cliente): ?>
                                <option value="<?php echo htmlspecialchars($cliente['id']); ?>">
                                    <?php echo htmlspecialchars($cliente['nombre']); ?> (<?php echo htmlspecialchars($cliente['email']); ?>)
                                </option>
                            <?php endforeach; 
                        else: ?>
                            <option disabled>No hay clientes registrados.</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección de Envío:</label>
                    <textarea id="direccion" name="direccion" required placeholder="Ingrese la dirección de envío"></textarea>
                </div>
            </div>

            <div class="seccion-pedido card p-4 mb-4">
                <h2>2. Añadir Productos</h2>
                
                <div id="items-container">
                    <div class="item-selection mb-3">
                        <div class="form-group flex-grow-1">
                            <label for="producto_1">Producto:</label>
                            <select name="items[1][producto_id]" id="producto_1" required>
                                <option value="">-- Seleccione un Producto --</option>
                                <?php 
                                // Asumimos que $productos_catalogo viene del controlador (ProductoModelo::obtenerTodos())
                                if (!empty($productos_catalogo)):
                                    foreach ($productos_catalogo as $prod): ?>
                                        <option value="<?php echo htmlspecialchars($prod['id']); ?>" data-precio="<?php echo htmlspecialchars($prod['precio']); ?>">
                                            <?php echo htmlspecialchars($prod['nombre']); ?> (S/. <?php echo number_format($prod['precio'], 2); ?>)
                                        </option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cantidad_1">Cantidad:</label>
                            <input type="number" name="items[1][cantidad]" id="cantidad_1" min="1" value="1" required style="width: 80px;">
                        </div>
                        <div class="form-group">
                            <label for="precio_1">Precio Venta:</label>
                            <input type="number" name="items[1][precio_unitario]" id="precio_1" step="0.01" min="0.01" required value="0.00" style="width: 100px;">
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-info mt-2"><i class="fa fa-plus"></i> Añadir Otro Ítem (Requiere JS)</button>
            </div>

            <div class="seccion-pedido card p-4">
                <h2>3. Finalizar Pedido</h2>
                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta (Terminal)</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <strong>Total Estimado:</strong> S/. 0.00 (Este cálculo requiere JavaScript)
                </div>
            </div>

            <div class="actions mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Crear Pedido</button>
                <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>