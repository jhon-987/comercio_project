<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/UsuarioController.php
 * CÓDIGO FINAL: Todas las correcciones de seguridad, lógica CRUD y rutas de vista aplicadas.
 */
require_once __DIR__ . '/../Modelos/Usuario.php'; 

class UsuarioController {
    private $usuarioModelo;

    public function __construct() {
        $this->usuarioModelo = new Usuario();
    }

    // ==========================================================
    // --- SEGURIDAD: VERIFICACIÓN DE ACCESO ADMIN/EMPLEADO ---
    // ==========================================================
    private function checkAdminAccess() {
        // CORREGIDO: Usa $_SESSION['rol']
        if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'empleado')) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=' . urlencode('Acceso denegado. Se requiere ser administrador o empleado.'));
            exit;
        }
    }

    // ==========================================================
    // --- LISTADO (verusuarios) ---
    // ==========================================================
    public function verusuarios($id = null) {
        $this->checkAdminAccess();
        
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        
        $usuarios = $this->usuarioModelo->obtenerTodos(); 
        
        // CORREGIDO: Usamos el nombre de vista que hemos confirmado que funciona
        require_once __DIR__ . '/../Vistas/admin/usuarios/verUsuario.php';
    }
    
    // ==========================================================
    // --- CREAR USUARIO (crearusuario) ---
    // ==========================================================
    public function crearusuario() {
        $this->checkAdminAccess();
        $error = null;
        $mensaje = null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            
            if ($nombre && $email && $password && $rol) {
                if ($this->usuarioModelo->registrarUsuario($nombre, $email, $password, $telefono, $rol)) {
                    header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&mensaje=' . urlencode('Usuario creado exitosamente.'));
                    exit;
                } else {
                    $error = "El correo electrónico ya está registrado o hubo un error al guardar.";
                }
            } else {
                $error = "Por favor, complete todos los campos obligatorios.";
            }
        }
        
        require_once __DIR__ . '/../Vistas/admin/usuarios/crearUsuario.php';
    }

    // ==========================================================
    // --- EDITAR USUARIO (editarusuario) ---
    // ==========================================================
    public function editarusuario($id) { 
        $this->checkAdminAccess();
        $error = null;
        $usuario_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        
        if (!$usuario_id) {
            header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&error=' . urlencode('ID de usuario no especificado.'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

            if ($nombre && $rol) {
                if ($this->usuarioModelo->actualizarUsuarioAdmin($usuario_id, $nombre, $rol, $telefono, $direccion)) {
                    header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&mensaje=' . urlencode('Usuario actualizado exitosamente.'));
                    exit;
                } else {
                    $error = "Error de base de datos al actualizar el usuario.";
                }
            } else {
                $error = "Por favor, complete todos los campos obligatorios.";
            }
        }
        
        $usuario = $this->usuarioModelo->obtenerPorId($usuario_id);
        
        if (!$usuario) {
            header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&error=' . urlencode('Usuario no encontrado.'));
            exit;
        }

        require_once __DIR__ . '/../Vistas/admin/usuarios/editarUsuario.php';
    }
    
    // ==========================================================
    // --- ELIMINAR USUARIO (eliminarusuario) ---
    // ==========================================================
    public function eliminarusuario() { 
        $this->checkAdminAccess();
        
        // Se usa 'id' porque el enlace DEBE usar el parámetro 'id'
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT); 
        $error = null;
        $mensaje = null;

        if ($id) {
            // Lógica de seguridad para evitar auto-eliminación
            if ($id == ($_SESSION['usuario_id'] ?? 0)) {
                $error = "No puedes eliminar el usuario con el que has iniciado sesión.";
            } else if ($this->usuarioModelo->eliminarUsuario($id)) {
                $mensaje = "Usuario eliminado exitosamente.";
            } else {
                $error = "Error al eliminar el usuario. Verifique si tiene pedidos asociados.";
            }
        } else {
            $error = "ID de usuario no especificado.";
        }

        // Redirigir SIEMPRE de vuelta al listado
        header('Location: ' . BASE_URL . 'index.php?c=usuario&a=verusuarios&mensaje=' . urlencode($mensaje ?? $error));
        exit;
    }
}