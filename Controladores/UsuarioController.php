<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/UsuarioController.php
 */
require_once __DIR__ . '/../Modelos/Usuario.php'; 

class UsuarioController {
    private $usuarioModelo;

    public function __construct() {
        $this->usuarioModelo = new Usuario();
    }

    public function verusuarios($id = null) {
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        
        // Lógica de eliminación (debes asegurar que el usuario no tenga pedidos)
        $eliminar_id = filter_input(INPUT_GET, 'eliminar_id', FILTER_SANITIZE_NUMBER_INT);
        if ($eliminar_id) {
             if ($this->usuarioModelo->eliminarUsuario($eliminar_id)) {
                 $mensaje = "Usuario eliminado exitosamente.";
             } else {
                 $error = "Error al eliminar el usuario. Verifique si tiene pedidos asociados.";
             }
             header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&mensaje=' . urlencode($mensaje ?? $error));
             exit;
        }

        $usuarios = $this->usuarioModelo->obtenerTodos(); 
        
        require_once __DIR__ . '/../Vistas/admin/usuarios/verUsuario.php';
    }
    
    // Aquí se implementarían editarusuario, crearusuario, etc., siguiendo la misma lógica.
}