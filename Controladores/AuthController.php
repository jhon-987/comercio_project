<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/AuthController.php
 * FINAL: Manejo de autenticación, incluyendo registro condicional de administrador.
 */
require_once __DIR__ . '/../Modelos/Usuario.php'; 

class AuthController {
    private $usuarioModelo;
    private $CODIGO_SECRETO_ADMIN = "ADMIN_CODE_777"; // <--- CÓDIGO CLAVE

    public function __construct() {
        $this->usuarioModelo = new Usuario(); 
    }

    public function login($id = null) {
        $error = null;
        $mensaje = $_GET['m'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 

            if ($email && $password) {
                $usuario = $this->usuarioModelo->validarLogin($email, $password);

                if ($usuario) {
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['rol'] = $usuario['rol'];
                    
                    if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'empleado') {
                        header('Location: index.php?c=admin&a=dashboard');
                    } else {
                        header('Location: index.php?c=cliente&a=catalogo');
                    }
                    exit;
                } else {
                    $error = "Credenciales incorrectas o usuario no encontrado.";
                }
            } else {
                $error = "Por favor, ingrese email y contraseña.";
            }
        }
        require_once __DIR__ . '/../Vistas/auth/login.php'; 
    }

    public function logout($id = null) {
        session_unset();
        session_destroy();
        header('Location: index.php?c=auth&a=login&m=Sesión cerrada exitosamente.');
        exit;
    }

    /**
     * Permite el registro como 'admin' si se usa el código secreto.
     */
    public function registro($id = null) {
        $error = null;
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            // Captura el código secreto del formulario
            $codigo_ingresado = filter_input(INPUT_POST, 'admin_code', FILTER_SANITIZE_STRING); 
            
            $rol_a_registrar = 'cliente'; // Valor por defecto

            // LÓGICA CONDICIONAL: Si el código coincide, eleva el rol
            if (!empty($codigo_ingresado) && $codigo_ingresado === $this->CODIGO_SECRETO_ADMIN) {
                $rol_a_registrar = 'admin';
            }

            if ($nombre && $email && $password && strlen($password) >= 6) {
                // Se llama al Modelo pasando el rol como 5to parámetro
                if ($this->usuarioModelo->registrarUsuario($nombre, $email, $password, $telefono, $rol_a_registrar)) {
                    header('Location: index.php?c=auth&a=login&m=Registro exitoso. Ahora puede iniciar sesión.');
                    exit;
                } else {
                    $error = "El correo electrónico ya está registrado o hubo un error en la base de datos.";
                }
            } else {
                $error = "Por favor, complete todos los campos y use una contraseña de 6 o más caracteres.";
            }
        }
        require_once __DIR__ . '/../Vistas/auth/registro.php';
    }
}