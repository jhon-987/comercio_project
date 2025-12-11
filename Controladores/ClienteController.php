<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/ClienteController.php
 * FINAL Y CORREGIDO: Implementa la lógica para pasar los datos completos del cliente al carrito.
 */
// Requerimiento de Composer: Incluir el autoload de la librería PDF (Dompdf)
require_once __DIR__ . '/../vendor/autoload.php'; 
use Dompdf\Dompdf; 

require_once __DIR__ . '/../Modelos/Producto.php'; 
require_once __DIR__ . '/../Modelos/Carrito.php'; 
require_once __DIR__ . '/../Modelos/Pedido.php'; 
require_once __DIR__ . '/../Modelos/Usuario.php'; 

class ClienteController {
    private $productoModelo;
    private $carritoModelo;
    private $pedidoModelo;
    private $usuarioModelo;

    public function __construct() {
        $this->productoModelo = new Producto();
        $this->carritoModelo = new Carrito(); 
        $this->pedidoModelo = new Pedido(); 
        $this->usuarioModelo = new Usuario(); 
    }

    // ==========================================================
    // ACCIONES Catálogo y Carrito
    // ==========================================================
    public function catalogo($id = null) {
        $search_term = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category_id = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;

        if (empty($category_id)) {
            $category_id = null;
        }

        $productos = $this->productoModelo->obtenerConFiltros($search_term, $category_id);
        $categorias = $this->productoModelo->obtenerCategorias();
        
        require_once __DIR__ . '/../Vistas/cliente/catalogo.php';
    }

    public function agregarcarrito($id = null) {
        $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_SANITIZE_NUMBER_INT);
        $nombre     = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $precio     = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $cantidad   = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
        
        $error = null;
        $mensaje = null;

        if ($idProducto && $precio !== false && $cantidad > 0) {
            
            $resultado = $this->carritoModelo->agregarItem($idProducto, $nombre, $precio, $cantidad);
            
            if ($resultado === true) {
                $mensaje = "Producto añadido al carrito.";
            } else if (is_string($resultado)) {
                $error = $resultado; 
            } else {
                $error = "Error desconocido al añadir el producto.";
            }
        } else {
            $error = "Datos inválidos para añadir al carrito.";
        }
        
        $redirect_url = BASE_URL . 'index.php?c=cliente&a=carrito';
        if ($mensaje) {
            $redirect_url .= '&mensaje=' . urlencode($mensaje);
        } else if ($error) {
            $redirect_url .= '&error=' . urlencode($error);
        }

        header('Location: ' . $redirect_url);
        exit;
    }

    /**
     * Muestra la vista del carrito, pasando los datos del cliente para autocompletado.
     */
    public function carrito($id = null) {
        $cliente = null; 
        
        // Obtener datos del cliente para pre-llenado
        if (isset($_SESSION['usuario_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            // Usar el Modelo de Usuario para obtener los datos completos
            $cliente = $this->usuarioModelo->obtenerPorId($usuario_id);
        }
        
        $carrito = $this->carritoModelo->obtenerItems();
        
        // La vista carrito.php ahora tendrá acceso a las variables $carrito y $cliente
        require_once __DIR__ . '/../Vistas/cliente/carrito.php';
    }
    
    public function confirmarPedido($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=Debes iniciar sesión para finalizar tu pedido.');
            exit;
        }

        $carrito = $this->carritoModelo->obtenerItems();
        if (empty($carrito)) {
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&error=El carrito está vacío.');
            exit;
        }

        $usuario_id = $_SESSION['usuario_id'];
        
        // Captura y saneamiento de datos de envío y pago
        $nombreCliente = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $direccion     = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $metodoPago    = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Variables de control de pago
        $referenciaPago = null;
        $pago_exitoso_simulado = true;
        $error_msg = "";

        // LÓGICA DE GESTIÓN DE PAGO (Validación)
        if ($metodoPago == 'tarjeta') {
            $numTarjeta = filter_input(INPUT_POST, 'num_tarjeta', FILTER_SANITIZE_NUMBER_INT);
            $expiracion = filter_input(INPUT_POST, 'expiracion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cvc        = filter_input(INPUT_POST, 'cvc', FILTER_SANITIZE_NUMBER_INT);

            $tarjetaGuardada = $_SESSION['tarjeta_guardada'] ?? null;
            
            if (!$tarjetaGuardada || !empty($numTarjeta)) {
                if (empty($numTarjeta) || empty($expiracion) || empty($cvc)) {
                    $error_msg = "Debes ingresar todos los datos de la tarjeta o usar una guardada.";
                    $pago_exitoso_simulado = false;
                } else {
                    $_SESSION['tarjeta_guardada'] = [
                        'ultimos_digitos' => substr($numTarjeta, -4),
                        'expiracion' => $expiracion,
                    ];
                }
            }
        } elseif ($metodoPago == 'yape') {
            $referenciaPago = filter_input(INPUT_POST, 'referencia_yape', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (empty($referenciaPago)) {
                $error_msg = "Debes ingresar el número de referencia/comprobante de Yape.";
                $pago_exitoso_simulado = false;
            }
        } elseif ($metodoPago == 'transferencia') {
            $referenciaPago = filter_input(INPUT_POST, 'referencia_transferencia', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (empty($referenciaPago)) {
                $error_msg = "Debes ingresar el número de referencia/comprobante de Transferencia Bancaria.";
                $pago_exitoso_simulado = false;
            }
        }
        
        if (!$pago_exitoso_simulado) {
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&error=' . urlencode($error_msg));
            exit;
        }

        // 🛑 Llama a PedidoModelo::crearPedido, que maneja la transacción y stock
        $pedido_id = $this->pedidoModelo->crearPedido(
            $usuario_id, 
            $carrito, 
            $metodoPago, 
            $direccion,
            $nombreCliente, 
            $referenciaPago 
        );
        
        if ($pedido_id) {
            $this->carritoModelo->vaciar();
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=misPedidos&mensaje=' . urlencode('¡Pedido completado con éxito! Puedes descargar tu factura aquí.'));
            exit;
            
        } else {
            $error_msg = "Error al procesar el pedido. Stock insuficiente o fallo de BD.";
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&error=' . urlencode($error_msg));
            exit;
        }
    }
    
    // ==========================================================
    // ACCIÓN HISTORIAL DE PEDIDOS Y PDF
    // ==========================================================
    public function misPedidos($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=Debes iniciar sesión para ver tus pedidos.');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];
        $pedidos = $this->pedidoModelo->obtenerPedidosPorUsuario($usuario_id);
        require_once __DIR__ . '/../Vistas/cliente/mis_pedidos.php';
    }

    public function descargarFactura() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=' . urlencode('Debes iniciar sesión.'));
            exit;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        $pedido_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (!$pedido_id) {
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=misPedidos&error=' . urlencode('ID de pedido no especificado.'));
            exit;
        }

        $pedido = $this->pedidoModelo->obtenerDetallePedido($pedido_id);

        if (!$pedido || ($pedido['usuario_id'] != $usuario_id)) {
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=misPedidos&error=' . urlencode('Acceso denegado o pedido no encontrado.'));
            exit;
        }

        $this->generarFacturaPdf($pedido); 
    }

    private function generarFacturaPdf($pedido) {
        
        $html = '<!DOCTYPE html><html><head><style>
                 body { font-family: sans-serif; font-size: 12px; }
                 h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 5px; } 
                 table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                 th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                 .total { text-align: right; font-weight: bold; background-color: #f0f0f0; }
                 .header-info { margin-bottom: 20px; }
                 </style></head><body>';
        
        $html .= '<h1>GUÍA DE SEGUIMIENTO / FACTURA Electrónica</h1>';
        $html .= '<div class="header-info">';
        $html .= '<p><strong>Código de Pedido:</strong> PED-' . htmlspecialchars($pedido['id']) . '</p>';
        $html .= '<p><strong>Fecha:</strong> ' . htmlspecialchars($pedido['fecha_pedido']) . '</p>';
        $html .= '</div>';

        $html .= '<h2>Datos de Envío:</h2>';
        $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($pedido['nombre_cliente'] ?? $pedido['nombre_usuario']) . '</p>';
        $html .= '<p><strong>Dirección de Envío:</strong> ' . htmlspecialchars($pedido['direccion_envio']) . '</p>';
        $html .= '<p><strong>Método de Pago:</strong> ' . htmlspecialchars($pedido['metodo_pago']) . '</p>';
        if (!empty($pedido['referencia_pago'])) {
            $html .= '<p><strong>Referencia Pago:</strong> ' . htmlspecialchars($pedido['referencia_pago']) . '</p>';
        }
        
        $html .= '<h2>Productos:</h2>';
        $html .= '<table><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unitario</th><th>Subtotal</th></tr></thead><tbody>';
        
        $total_items = 0;
        foreach ($pedido['items'] as $item) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total_items += $subtotal;
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['nombre_producto']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['cantidad']) . '</td>';
            $html .= '<td>S/. ' . number_format($item['precio_unitario'], 2) . '</td>';
            $html .= '<td>S/. ' . number_format($subtotal, 2) . '</td>';
            $html .= '</tr>';
        }

        $html .= '<tr><td colspan="3" class="total">TOTAL FINAL:</td><td class="total">S/. ' . number_format($total_items, 2) . '</td></tr>';
        $html .= '</tbody></table></body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream("Guia_Factura_PED_" . $pedido['id'] . ".pdf", array("Attachment" => false)); 
        
        exit; 
    }
    
    // ==========================================================
    // --- ACCIONES DE PERFIL Y CONTRASEÑA ---
    // ==========================================================
    public function perfil($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=Debes iniciar sesión para ver tu perfil.');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];
        $cliente = $this->usuarioModelo->obtenerPorId($usuario_id); 
        if (!$cliente) {
             header('Location: ' . BASE_URL . 'index.php?c=auth&a=logout');
             exit;
        }
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../Vistas/cliente/perfil.php';
    }
    
    public function actualizarperfil($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];
        
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $numTarjetaPerfil = filter_input(INPUT_POST, 'num_tarjeta_perfil', FILTER_SANITIZE_NUMBER_INT);
        $expiracionPerfil = filter_input(INPUT_POST, 'expiracion_perfil', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $mensaje = "Perfil actualizado correctamente.";
        
        if (!empty($numTarjetaPerfil) && !empty($expiracionPerfil)) {
            $_SESSION['tarjeta_guardada'] = [
                'ultimos_digitos' => substr($numTarjetaPerfil, -4),
                'expiracion' => $expiracionPerfil,
            ];
            $mensaje = "Perfil y tarjeta actualizados correctamente.";
        }
        
        $this->usuarioModelo->actualizarPerfil($usuario_id, $nombre, $telefono, $direccion);
        header('Location: ' . BASE_URL . 'index.php?c=cliente&a=perfil&mensaje=' . urlencode($mensaje));
        exit;
    }
    
    public function cambiarpassword($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login');
            exit;
        }
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../Vistas/cliente/cambiar_password.php';
    }

    public function actualizarPassword() {
        if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login');
            exit;
        }
        
        $usuario_id = $_SESSION['usuario_id'];
        
        $actual_password = filter_input(INPUT_POST, 'actual_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $nueva_password = filter_input(INPUT_POST, 'nueva_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirmacion_password = filter_input(INPUT_POST, 'confirmacion_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $error = null;
        $mensaje = null;

        if (empty($actual_password) || empty($nueva_password) || empty($confirmacion_password)) {
            $error = "Todos los campos son obligatorios.";
        } elseif ($nueva_password !== $confirmacion_password) {
            $error = "La nueva contraseña y la confirmación no coinciden.";
        } elseif (strlen($nueva_password) < 6) {
            $error = "La nueva contraseña debe tener al menos 6 caracteres.";
        } else {
            if ($this->usuarioModelo->actualizarPassword($usuario_id, $actual_password, $nueva_password)) {
                $mensaje = "Contraseña actualizada exitosamente.";
            } else {
                $error = "La contraseña actual es incorrecta o hubo un error de base de datos.";
            }
        }
        
        $redirect_url = BASE_URL . 'index.php?c=cliente&a=cambiarpassword';
        if ($mensaje) {
            $redirect_url .= '&mensaje=' . urlencode($mensaje);
        } else if ($error) {
            $redirect_url .= '&error=' . urlencode($error);
        }
        
        header('Location: ' . $redirect_url);
        exit;
    }
}