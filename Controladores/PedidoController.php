<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/PedidoController.php
 * CORREGIDO Y FINAL: CRUD de pedidos para el administrador.
 */

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Modelos/Pedido.php'; 
require_once __DIR__ . '/../Modelos/Usuario.php'; 
require_once __DIR__ . '/../Modelos/Producto.php'; 

class PedidoController {
    private $pedidoModelo;
    private $usuarioModelo;
    private $productoModelo;

    public function __construct() {
        $this->pedidoModelo = new Pedido();
        $this->usuarioModelo = new Usuario();
        $this->productoModelo = new Producto();
    }

    // ==========================================================
    // --- SEGURIDAD: VERIFICACIÓN DE ACCESO ADMIN/EMPLEADO ---
    // ==========================================================
    private function checkAdminAccess() {
        // 🛑 CORRECCIÓN CLAVE: Usar $_SESSION['rol']
        if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'empleado')) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=' . urlencode('Acceso denegado. Se requiere ser administrador.'));
            exit;
        }
    }

    // ==========================================================
    // --- LECTURA: LISTADO DE PEDIDOS (verpedidos) ---
    // ==========================================================
    public function verpedidos($id = null) {
        $this->checkAdminAccess(); // Aplicar seguridad
        
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        
        // Lógica de eliminación 
        $eliminar_id = filter_input(INPUT_GET, 'eliminar_id', FILTER_SANITIZE_NUMBER_INT);
        if ($eliminar_id) {
            if ($this->pedidoModelo->eliminarPedido($eliminar_id)) {
                 $mensaje = "Pedido eliminado exitosamente.";
            } else {
                 $error = "Error al eliminar el pedido. Podría estar asociado a restricciones.";
            }
            header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&mensaje=' . urlencode($mensaje ?? $error));
            exit;
        }

        $pedidos = $this->pedidoModelo->obtenerTodosPedidosAdmin(); 
        
        require_once __DIR__ . '/../Vistas/admin/pedidos/verPedidos.php';
    }

    // ==========================================================
    // --- CREACIÓN MANUAL DE PEDIDO (crearpedidos) ---
    // ==========================================================
    public function crearpedidos($id = null) {
        $this->checkAdminAccess(); // Aplicar seguridad
        
        $error = null;
        $mensaje = null;
        
        $clientes = $this->usuarioModelo->obtenerClientes(); 
        $productos_catalogo = $this->productoModelo->obtenerTodos(); 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $usuario_id = filter_input(INPUT_POST, 'usuario_id', FILTER_SANITIZE_NUMBER_INT);
            $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $metodo_pago = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $items_post = $_POST['items'] ?? []; 
            
            // 1. Validar datos mínimos
            if (empty($usuario_id) || empty($direccion) || empty($items_post)) {
                $error = "Faltan datos obligatorios (cliente, dirección o productos).";
            } else {
                
                // 2. Transformar el array POST de ítems a la estructura de 'carrito'
                $carrito_simulado = [];
                $cliente_data = $this->usuarioModelo->obtenerPorId($usuario_id);
                $nombreCliente = $cliente_data['nombre'] ?? 'Cliente Manual';
                
                $todos_items_validos = true;
                foreach ($items_post as $item_data) {
                    $prod_id = filter_var($item_data['producto_id'], FILTER_SANITIZE_NUMBER_INT);
                    $cantidad = filter_var($item_data['cantidad'], FILTER_SANITIZE_NUMBER_INT);
                    $precio = filter_var($item_data['precio_unitario'], FILTER_VALIDATE_FLOAT);
                    
                    if ($prod_id && $cantidad > 0 && $precio !== false && $precio > 0) {
                        $carrito_simulado[] = [
                            'id_producto' => $prod_id,
                            'precio' => $precio, 
                            'cantidad' => $cantidad
                        ];
                    } else if ($prod_id) {
                               $todos_items_validos = false;
                    }
                }
                
                // 3. Crear el pedido si hay ítems válidos
                if (!empty($carrito_simulado) && $todos_items_validos) {
                    $pedido_id = $this->pedidoModelo->crearPedido(
                        $usuario_id, 
                        $carrito_simulado, 
                        $metodo_pago, 
                        $direccion, 
                        $nombreCliente, 
                        'PEDIDO MANUAL' 
                    );

                    if ($pedido_id) {
                        header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&mensaje=' . urlencode('Pedido manual creado exitosamente. ID: ' . $pedido_id));
                        exit;
                    } else {
                        $error = "Error al procesar el pedido o stock insuficiente.";
                    }
                } else {
                    $error = "Debe añadir al menos un ítem válido al pedido.";
                }
            }
        }

        require_once __DIR__ . '/../Vistas/admin/pedidos/crearPedidos.php';
    }

    // ==========================================================
    // --- EDICIÓN/DETALLE (editarpedidos) ---
    // ==========================================================
    public function editarpedidos($id) {
        $this->checkAdminAccess(); // Aplicar seguridad
        $pedido_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if (!$pedido_id) {
            header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&error=ID de pedido no especificado.');
            exit;
        }

        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lógica para actualizar el estado del pedido
            $nuevo_estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
            
            if ($this->pedidoModelo->actualizarEstadoPedido($pedido_id, $nuevo_estado)) {
                $mensaje = "Estado de pedido #{$pedido_id} actualizado a " . ucfirst($nuevo_estado) . ".";
                header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&mensaje=' . urlencode($mensaje));
                exit;
            } else {
                $error = "Error al actualizar el estado del pedido.";
            }
        }

        // Cargar el detalle completo del pedido para mostrar la vista
        $pedido = $this->pedidoModelo->obtenerDetallePedido($pedido_id); 

        if (!$pedido) {
            header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&error=' . urlencode('Pedido no encontrado.'));
            exit;
        }

        require_once __DIR__ . '/../Vistas/admin/pedidos/editarPedidos.php';
    }
}