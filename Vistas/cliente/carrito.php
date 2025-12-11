<?php
// VISTA: /Vistas/cliente/carrito.php
include 'menu_cliente.php'; 

$carrito = $carrito ?? [];
$total_carrito = 0;
// NOTA: Se asume que el Controlador pasa $cliente con los datos de sesión/DB.
// Si no lo hace, debes añadir la lógica de $cliente = $this->usuarioModelo->obtenerPorId($_SESSION['usuario_id']); en ClienteController::carrito()
$cliente_direccion = $cliente['direccion'] ?? ''; 
$cliente_nombre = $_SESSION['usuario_nombre'] ?? '';

// Obtener tarjeta guardada para la vista
$tarjeta_guardada = $_SESSION['tarjeta_guardada'] ?? null; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/estilos.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/pages/cliente-carrito.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* Estilos generales mejorados */
        .metodo-details {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .metodo-details.hidden {
            display: none;
        }
        .form-group.group-inline {
            display: flex;
            gap: 20px;
        }
        .form-group.group-inline > div {
            flex: 1;
        }
        /* Limpiar floats, aunque se usa flexbox */
        .float-clear {
            clear: both;
        }
        .cantidad-input {
            width: 60px; 
            text-align: center;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
<div class="container main-content">
    <h1><i class="fa fa-shopping-cart"></i> Carrito de Compras</h1>

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['mensaje'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php endif; ?>

    <?php if (empty($carrito)): ?>
        <p class="alert alert-info">
            Tu carrito está vacío. <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=catalogo">Volver al catálogo</a>
        </p>
    <?php else: ?>
        <p class="alert alert-success">¡Listo para finalizar tu compra, **<?php echo htmlspecialchars(explode(' ', $cliente_nombre)[0]); ?>**!</p>

        <div class="carrito-wrapper">

            <div class="tabla-carrito-wrapper">
                <table class="tabla-carrito">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carrito as $key => $item):
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_carrito += $subtotal;
                        ?>
                        <tr>
                            <td class="producto-nombre"><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td class="precio">S/. <?php echo number_format($item['precio'], 2); ?></td>
                            
                            <td class="cantidad">
                                <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=carrito&a=actualizar" style="display: inline;">
                                    <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($item['id_producto']); ?>">
                                    
                                    <input 
                                        type="number" 
                                        name="cantidad" 
                                        value="<?php echo htmlspecialchars($item['cantidad']); ?>" 
                                        min="1" 
                                        required
                                        class="cantidad-input"
                                        title="Cambiar cantidad y presionar Enter"
                                        onchange="this.form.submit()" 
                                    >
                                </form>
                            </td>
                            <td class="subtotal">S/. <?php echo number_format($subtotal, 2); ?></td>
                            <td class="acciones">
                                <a href="<?php echo BASE_URL; ?>index.php?c=carrito&a=eliminaritem&id=<?php echo htmlspecialchars($key); ?>" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="return confirm('¿Eliminar este producto?');">
                                    <i class="fa fa-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right total-label">TOTAL FINAL:</td>
                            <td colspan="2" class="total-valor">S/. <?php echo number_format($total_carrito, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <hr>

            <h2>Datos de Envío y Pago</h2>
            <form method="POST" action="<?php echo BASE_URL; ?>index.php?c=cliente&a=confirmarPedido" class="form-checkout">
                
                <div class="form-group">
                    <label for="nombre">Nombre (Receptor):</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente_nombre); ?>" placeholder="Tu nombre completo" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección de Envío:</label>
                    <textarea id="direccion" name="direccion" placeholder="Calle, número, distrito, ciudad" required><?php echo htmlspecialchars($cliente_direccion); ?></textarea>
                    <?php if (!empty($cliente_direccion)): ?>
                        <small class="text-muted">Dirección pre-llenada desde tu <a href="<?php echo BASE_URL; ?>index.php?c=cliente&a=perfil">Perfil</a>.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required>
                        <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                        <option value="yape">Yape</option>
                        <option value="transferencia">Transferencia Bancaria</option>
                        <option value="efectivo">Pago en Efectivo (Contra Entrega)</option>
                    </select>
                </div>
                
                <div id="datos_tarjeta" class="metodo-details">
                    <h4><i class="fa fa-credit-card"></i> Pago con Tarjeta (Pruebas)</h4>
                    
                    <?php if ($tarjeta_guardada): ?>
                        <div class="alert alert-info">
                            Tarjeta guardada lista para usar: **** **** **** **<?php echo htmlspecialchars($tarjeta_guardada['ultimos_digitos']); ?>** (Exp: **<?php echo htmlspecialchars($tarjeta_guardada['expiracion']); ?>**). 
                            <br>Si deseas usar esta tarjeta, **deja los campos de abajo vacíos**. Si deseas usar otra, rellénalos.
                        </div>
                    <?php else: ?>
                        <p class="text-danger">**No** tienes una tarjeta registrada. Por favor, ingresa los datos de tu tarjeta a continuación.</p>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="num_tarjeta">Número de Tarjeta (Nuevo):</label>
                        <input type="text" id="num_tarjeta" name="num_tarjeta" 
                               placeholder="XXXX XXXX XXXX XXXX" 
                               pattern="\d{13,16}" 
                               title="Ingrese entre 13 y 16 dígitos.">
                    </div>

                    <div class="form-group group-inline">
                        <div>
                             <label for="expiracion">Expiración (MM/AA):</label>
                             <input type="text" id="expiracion" name="expiracion" 
                                     placeholder="MM/AA" 
                                     pattern="\d{2}\/\d{2}"
                                     title="Formato: MM/AA (Ej: 12/26)">
                        </div>
                        <div>
                            <label for="cvc">CVC/CVV:</label>
                            <input type="text" id="cvc" name="cvc" 
                                    placeholder="123" 
                                    pattern="\d{3,4}"
                                    title="3 o 4 dígitos de seguridad.">
                        </div>
                    </div>
                </div>
                
                <div id="datos_yape" class="metodo-details hidden">
                    <h4><i class="fa fa-mobile-alt"></i> Pago con Yape</h4>
                    <div class="alert alert-info">
                        Por favor, Yapea el monto total (S/. <?php echo number_format($total_carrito, 2); ?>) al número 987654321. Una vez hecho, ingresa la referencia.
                        <div class="form-group">
                            <label for="referencia_yape">Número de Operación/Referencia Yape:</label>
                            <input type="text" id="referencia_yape" name="referencia_yape" placeholder="Ingresa el código de 8 dígitos de Yape">
                        </div>
                    </div>
                </div>

                <div id="datos_transferencia" class="metodo-details hidden">
                    <h4><i class="fa fa-building"></i> Transferencia Bancaria</h4>
                    <div class="alert alert-warning">
                        Realiza una transferencia al siguiente número de cuenta (Banco BCP):
                        <ul>
                            <li>Cuenta BCP (Soles): 191-3456789-0-22</li>
                            <li>Titular: COMERCIO S.A.C.</li>
                        </ul>
                        <div class="form-group">
                            <label for="referencia_transferencia">Adjuntar Comprobante o Referencia:</label>
                            <input type="text" id="referencia_transferencia" name="referencia_transferencia" placeholder="Ingresa el número de operación">
                        </div>
                    </div>
                </div>
                
                <div id="datos_efectivo" class="metodo-details hidden">
                    <h4><i class="fa fa-money-bill-wave"></i> Pago en Efectivo</h4>
                    <div class="alert alert-success">
                        Pagarás S/. **<?php echo number_format($total_carrito, 2); ?>** al recibir tu pedido. Por favor, asegúrate de tener el monto exacto.
                    </div>
                </div>


                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fa fa-check-circle"></i> Confirmar Pedido
                </button>
            </form>

        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const metodoPagoSelect = document.getElementById('metodo_pago');
        const detallesMetodo = {
            'tarjeta': document.getElementById('datos_tarjeta'),
            'yape': document.getElementById('datos_yape'),
            'transferencia': document.getElementById('datos_transferencia'),
            'efectivo': document.getElementById('datos_efectivo')
        };
        const todosLosMetodos = document.querySelectorAll('.metodo-details');
        
        // Usamos JSON.parse para convertir la cadena JSON de PHP a un objeto JS.
        const tarjetaGuardada = <?php echo json_encode($tarjeta_guardada ?? null); ?>; 

        // Campos de tarjeta
        const numTarjetaInput = document.getElementById('num_tarjeta');
        const expiracionInput = document.getElementById('expiracion');
        const cvcInput = document.getElementById('cvc');

        // Campos de referencia (Yape/Transferencia)
        const refYapeInput = document.getElementById('referencia_yape');
        const refTransferenciaInput = document.getElementById('referencia_transferencia');


        function setRequired(input, isRequired) {
             if(input) input.required = isRequired;
        }

        function toggleMetodoFields() {
            const selectedValue = metodoPagoSelect.value;

            // 1. Ocultar todos los bloques y remover 'required' de todos los campos
            todosLosMetodos.forEach(div => {
                div.classList.add('hidden');
            });
            
            // Remover required de todos los inputs controlados (Tarjeta y Referencias)
            [numTarjetaInput, expiracionInput, cvcInput, refYapeInput, refTransferenciaInput].forEach(input => {
                setRequired(input, false);
            });


            // 2. Mostrar el bloque de detalles seleccionado y establecer 'required'
            if (detallesMetodo[selectedValue]) {
                detallesMetodo[selectedValue].classList.remove('hidden');
                
                if (selectedValue === 'tarjeta') {
                    
                    const checkCardRequired = () => {
                        const isFilling = numTarjetaInput.value || expiracionInput.value || cvcInput.value;
                        const isRequired = isFilling || (tarjetaGuardada === null || tarjetaGuardada === false);

                        // Si está llenando O no hay tarjeta guardada, los tres campos son requeridos.
                        setRequired(numTarjetaInput, isRequired);
                        setRequired(expiracionInput, isRequired);
                        setRequired(cvcInput, isRequired);

                        // Si hay tarjeta guardada Y el usuario NO está llenando, los campos son opcionales
                        if (tarjetaGuardada && !isFilling) {
                            setRequired(numTarjetaInput, false);
                            setRequired(expiracionInput, false);
                            setRequired(cvcInput, false);
                        }
                    };

                    // Aplicar la lógica inmediatamente
                    checkCardRequired();
                    
                    // 3. Asociar la lógica a los eventos input
                    numTarjetaInput.oninput = checkCardRequired;
                    expiracionInput.oninput = checkCardRequired;
                    cvcInput.oninput = checkCardRequired;

                } else if (selectedValue === 'yape') {
                    // Forzar 'required' en la referencia de Yape
                    setRequired(refYapeInput, true);
                } else if (selectedValue === 'transferencia') {
                    // Forzar 'required' en la referencia de Transferencia
                    setRequired(refTransferenciaInput, true);
                }
            }
        }

        // Inicializar y escuchar cambios
        toggleMetodoFields(); 
        metodoPagoSelect.addEventListener('change', toggleMetodoFields);
    });
</script>

</body>
</html>