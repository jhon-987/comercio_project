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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .item-selection { display: flex; gap: 15px; align-items: flex-end; }
        .item-selection .form-group { flex-grow: 1; margin-bottom: 0; }
        .total-box { font-size: 1.2em; font-weight: bold; }
    </style>
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
                    <select id="usuario_id" name="usuario_id" required class="form-control">
                        <option value="">-- Seleccione un Cliente --</option>
                        <?php 
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
                <h2>2. Ítems del Pedido</h2>
                
                <div id="items-container">
                    <div class="item-selection mb-3" data-item-id="1">
                        <div class="form-group flex-grow-1">
                            <label for="producto_1">Producto:</label>
                            <select name="items[1][producto_id]" id="producto_1" required onchange="updatePrice(1)" class="form-control">
                                <option value="">-- Seleccione un Producto --</option>
                                <?php 
                                if (!empty($productos_catalogo)):
                                    foreach ($productos_catalogo as $prod): ?>
                                        <option value="<?php echo htmlspecialchars($prod['id']); ?>" 
                                                data-precio="<?php echo htmlspecialchars($prod['precio']); ?>">
                                            <?php echo htmlspecialchars($prod['nombre']); ?> (Stock: <?php echo htmlspecialchars($prod['stock']); ?>)
                                        </option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cantidad_1">Cantidad:</label>
                            <input type="number" name="items[1][cantidad]" id="cantidad_1" min="1" value="1" required 
                                   oninput="calculateTotal()" style="width: 80px;" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="precio_1">Precio Venta:</label>
                            <input type="number" name="items[1][precio_unitario]" id="precio_1" step="0.01" min="0.01" required value="0.00" 
                                   oninput="calculateTotal()" style="width: 100px;" class="form-control">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(1)" style="height: 38px;">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <button type="button" class="btn btn-info mt-2" onclick="addItem()"><i class="fa fa-plus"></i> Añadir Otro Ítem</button>
            </div>

            <div class="seccion-pedido card p-4">
                <h2>3. Resumen y Finalización</h2>
                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta (Terminal)</option>
                    </select>
                </div>
                
                <div class="alert alert-info total-box mt-3">
                    <strong>Total Final:</strong> S/. <span id="total-estimado">0.00</span>
                </div>
            </div>

            <div class="actions mt-4">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Crear Pedido</button>
                <a href="<?php echo BASE_URL; ?>index.php?c=admin&a=dashboard" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        let itemIdCounter = 1;

        // Función para clonar y añadir un nuevo ítem
        function addItem() {
            itemIdCounter++;
            const container = document.getElementById('items-container');
            const baseItem = document.querySelector('.item-selection[data-item-id="1"]');
            const newItem = baseItem.cloneNode(true);

            // Actualizar IDs y nombres para el nuevo índice
            newItem.setAttribute('data-item-id', itemIdCounter);
            
            newItem.querySelectorAll('[id]').forEach(element => {
                element.id = element.id.replace('1', itemIdCounter);
            });
            newItem.querySelectorAll('[name]').forEach(element => {
                element.name = element.name.replace('[1]', `[${itemIdCounter}]`);
            });
            
            // Actualizar handlers de eventos
            newItem.querySelector('select').setAttribute('onchange', `updatePrice(${itemIdCounter})`);
            newItem.querySelector('input[name*="cantidad"]').setAttribute('oninput', 'calculateTotal()');
            newItem.querySelector('input[name*="precio_unitario"]').setAttribute('oninput', 'calculateTotal()');
            
            // Reestablecer valores
            newItem.querySelector('input[name*="cantidad"]').value = 1;
            newItem.querySelector('input[name*="precio_unitario"]').value = '0.00';

            // Añadir botón de remover
            newItem.querySelector('button.btn-danger').setAttribute('onclick', `removeItem(${itemIdCounter})`);
            
            container.appendChild(newItem);
        }

        // Función para remover un ítem
        function removeItem(itemId) {
            const itemElement = document.querySelector(`.item-selection[data-item-id="${itemId}"]`);
            if (itemElement) {
                itemElement.remove();
                calculateTotal();
            }
        }

        // Función para actualizar el precio de venta al seleccionar un producto
        function updatePrice(itemId) {
            const selectElement = document.querySelector(`#producto_${itemId}`);
            const priceInput = document.querySelector(`#precio_${itemId}`);
            
            if (selectElement && priceInput) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const price = selectedOption.getAttribute('data-precio') || '0.00';
                priceInput.value = parseFloat(price).toFixed(2);
            }
            calculateTotal();
        }

        // Función principal para calcular el total
        function calculateTotal() {
            let total = 0;
            const items = document.querySelectorAll('#items-container .item-selection');
            
            items.forEach(item => {
                const quantityInput = item.querySelector('input[name*="cantidad"]');
                const priceInput = item.querySelector('input[name*="precio_unitario"]');
                
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                
                total += quantity * price;
            });

            document.getElementById('total-estimado').textContent = total.toFixed(2);
        }

        // Inicializar el cálculo al cargar la página
        window.onload = function() {
            calculateTotal();
        };

    </script>
</body>
</html>