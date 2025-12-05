<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/ClienteController.php
 */
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

    // --- ACCIONES PÚBLICAS (Catálogo y Carrito) ---
    public function catalogo($id = null) {
        $productos = $this->productoModelo->obtenerTodos(); 
        require_once __DIR__ . '/../Vistas/cliente/catalogo.php';
    }

    public function agregarcarrito($id = null) {
        $idProducto = filter_input(INPUT_POST, 'id_producto', FILTER_SANITIZE_NUMBER_INT);
        $nombre     = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $precio     = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
        $cantidad   = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
        
        if ($idProducto && $precio !== false && $cantidad > 0) {
            $this->carritoModelo->agregarItem($idProducto, $nombre, $precio, $cantidad);
        }
        header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito');
        exit;
    }

    public function carrito($id = null) {
        $carrito = $this->carritoModelo->obtenerItems();
        require_once __DIR__ . '/../Vistas/cliente/carrito.php';
    }
    
    public function confirmarPedido($id = null) {
        // La validación de sesión para checkout es crítica
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
        $nombreCliente = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $direccion     = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
        $metodoPago    = filter_input(INPUT_POST, 'metodo_pago', FILTER_SANITIZE_STRING);
        
        $pedido_id = $this->pedidoModelo->crearPedido($usuario_id, $carrito, $metodoPago, $direccion);
        
        if ($pedido_id) {
            $this->carritoModelo->vaciar();
            $total = $this->pedidoModelo->obtenerTotalPorId($pedido_id); 
            $codigoPedido = 'PED-' . $pedido_id;

            require_once __DIR__ . '/../Vistas/cliente/pedido_confirmado.php';
        } else {
            $error_msg = "Error al procesar el pedido. Stock insuficiente o fallo de BD.";
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&error=' . urlencode($error_msg));
            exit;
        }
    }
    
    // --- ACCIONES DE PERFIL ---
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
        $mensaje = "";
        require_once __DIR__ . '/../Vistas/cliente/perfil.php';
    }
    
    public function actualizarperfil($id = null) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING); // Asumiendo que el campo dirección fue añadido al form
        
        $this->usuarioModelo->actualizarPerfil($usuario_id, $nombre, $telefono, $direccion);
        $mensaje = "Perfil actualizado correctamente.";
        
        // Recargar la vista de perfil para mostrar los cambios
        $this->perfil();
    }
    
    public function cambiarpassword($id = null) {
        // La lógica completa de POST para cambiar contraseña debe ir aquí.
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login');
            exit;
        }
        $mensaje = null;
        $error = null;
        
        // Asumiendo que existe la vista /Vistas/cliente/cambiar_password.php
        require_once __DIR__ . '/../Vistas/cliente/cambiar_password.php';
    }
}