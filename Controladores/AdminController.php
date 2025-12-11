<?php
/**
 * COMERCIO_PROJECT-MASTER/Controladores/AdminController.php
 * Dashboard, Gestión de Pedidos y Usuarios.
 */
require_once __DIR__ . '/../Modelos/Pedido.php'; 
require_once __DIR__ . '/../Modelos/Usuario.php'; 

class AdminController {
    private $pedidoModelo;
    private $usuarioModelo;

    public function __construct() {
        $this->pedidoModelo = new Pedido();
        $this->usuarioModelo = new Usuario();
    }

    // ==========================================================
    // --- SEGURIDAD: VERIFICACIÓN DE ACCESO ---
    // ==========================================================
    private function checkAdminAccess() {
        // Usar $_SESSION['rol'] - acceso a panel admin
        if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'empleado')) {
            // Si el rol no es admin ni empleado, redirigir
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=' . urlencode('Acceso denegado. Se requiere ser administrador.'));
            exit;
        }
    }

    // ==========================================================
    // --- DASHBOARD - Vista principal ---
    // ==========================================================
    public function dashboard($id = null) {
        $this->checkAdminAccess();
        $titulo = "Panel de Administración";
        // Aquí podrías obtener métricas clave
        require_once __DIR__ . '/../Vistas/admin/dashboard.php';
    }

    // ==========================================================
    // --- GESTIÓN DE PEDIDOS ---
    // ==========================================================
    public function gestionarPedidos() {
        $this->checkAdminAccess();
        $titulo = "Gestión de Pedidos";

        $pedidos = $this->pedidoModelo->obtenerTodosPedidosAdmin();
        $mensaje = $_GET['mensaje'] ?? null;

        require_once __DIR__ . '/../Vistas/admin/pedidos_lista.php';
    }

    public function actualizarEstado() {
        $this->checkAdminAccess();

        $pedido_id = filter_input(INPUT_POST, 'pedido_id', FILTER_SANITIZE_NUMBER_INT);
        $nuevo_estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        if ($pedido_id && !empty($nuevo_estado)) {
            if ($this->pedidoModelo->actualizarEstadoPedido($pedido_id, $nuevo_estado)) {
                $mensaje = "Estado del pedido #{$pedido_id} actualizado a '" . ucfirst($nuevo_estado) . "'.";
            } else {
                $mensaje = "Error al actualizar el estado del pedido.";
            }
        } else {
            $mensaje = "Datos incompletos o inválidos.";
        }

        header('Location: ' . BASE_URL . 'index.php?c=admin&a=gestionarPedidos&mensaje=' . urlencode($mensaje));
        exit;
    }

    // Ver el detalle de un pedido específico
    public function verDetallePedido() {
        $this->checkAdminAccess();
        
        $pedido_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$pedido_id) {
            header('Location: ' . BASE_URL . 'index.php?c=admin&a=gestionarPedidos&error=' . urlencode('ID de pedido no especificado.'));
            exit;
        }

        // Usa la función obtenerDetallePedido del Modelo Pedido
        $pedido = $this->pedidoModelo->obtenerDetallePedido($pedido_id);
        
        if (!$pedido) {
            header('Location: ' . BASE_URL . 'index.php?c=admin&a=gestionarPedidos&error=' . urlencode('Pedido no encontrado.'));
            exit;
        }

        $titulo = "Detalle del Pedido #{$pedido_id}";
        require_once __DIR__ . '/../Vistas/admin/pedido_detalle.php';
    }

    // ==========================================================
    // --- GESTIÓN DE CLIENTES/USUARIOS ---
    // ==========================================================
    // Listado de Clientes
    public function gestionarClientes() {
        $this->checkAdminAccess();
        $titulo = "Gestión de Clientes";

        $clientes = $this->usuarioModelo->obtenerClientes(); 
        
        $mensaje = $_GET['mensaje'] ?? null;

        require_once __DIR__ . '/../Vistas/admin/clientes_lista.php';
    }
}