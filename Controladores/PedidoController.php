<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/PedidoController.php
 */
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

    public function verpedidos($id = null) {
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

    public function crearpedidos($id = null) {
        $error = null;
        $mensaje = null;
        
        $clientes = $this->usuarioModelo->obtenerClientes(); 
        $productos_catalogo = $this->productoModelo->obtenerTodos(); 
        
        // Lógica de procesamiento POST para crear un pedido manual (omito la lógica compleja de guardar el array de ítems)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $error = "La lógica de guardar el pedido manual es compleja y se omitió. Implementar aquí la transacción.";
        }

        require_once __DIR__ . '/../Vistas/admin/pedidos/crearPedidos.php';
    }

    public function editarpedidos($id) {
        $pedido_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if (!$pedido_id) {
            header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&error=ID de pedido no especificado.');
            exit;
        }

        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nuevo_estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
            
            if ($this->pedidoModelo->actualizarEstadoPedido($pedido_id, $nuevo_estado)) {
                header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&mensaje=' . urlencode('Estado de pedido actualizado a ' . $nuevo_estado . '.'));
                exit;
            } else {
                 $error = "Error al actualizar el estado del pedido.";
            }
        }

        $pedido = $this->pedidoModelo->obtenerDetallePedido($pedido_id); 

        if (!$pedido) {
            header('Location: ' . BASE_URL . 'index.php?c=pedido&a=verpedidos&error=Pedido no encontrado.');
            exit;
        }

        require_once __DIR__ . '/../Vistas/admin/pedidos/editarPedidos.php';
    }
}