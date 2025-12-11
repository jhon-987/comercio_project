<?php
/**
 * COMERCIO_PROJECT-MASTER/Controladores/AuthController.php
 * Manejo de autenticación, registro y redirección condicional por rol.
 */
require_once __DIR__ . '/../Modelos/Usuario.php';

class AuthController {
    private $usuarioModelo;
    // Código secreto para registrar administradores directamente desde el formulario
    private $CODIGO_SECRETO_ADMIN = "ADMIN_CODE_777"; 

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
                    
                    // Usamos 'usuario_nombre' y 'rol' consistentemente.
                    $_SESSION['usuario_nombre'] = $usuario['nombre']; 
                    $_SESSION['rol'] = $usuario['rol'];
                    
                    // Redirección basada en el rol al panel admin/cliente
                    if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'empleado') {
                        header('Location: ' . BASE_URL . 'index.php?c=admin&a=dashboard');
                    } else {
                        header('Location: ' . BASE_URL . 'index.php?c=cliente&a=catalogo');
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
        // Limpia y destruye TODAS las variables de sesión
        session_unset();
        session_destroy();
        // Redirige a la página de login con mensaje de cierre
        header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&m=' . urlencode('Sesión cerrada exitosamente.'));
        exit;
    }

    /**
     * Permite el registro estándar de cliente y registro condicional como 'admin' o 'empleado' 
     * si se usa el código secreto.
     */
    public function registro($id = null) {
        $error = null;
        $mensaje = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // Captura el código secreto del formulario ADMIN_CODE_777
            $codigo_ingresado = filter_input(INPUT_POST, 'admin_code', FILTER_SANITIZE_SPECIAL_CHARS); 
            // Valor por defecto
            $rol_a_registrar = 'cliente';

            // LÓGICA CONDICIONAL: Si el código coincide, eleva el rol
            if (!empty($codigo_ingresado) && $codigo_ingresado === $this->CODIGO_SECRETO_ADMIN) {
                // Podrías registrarlo como 'empleado' o 'admin' dependiendo de tu necesidad:
                $rol_a_registrar = 'admin'; 
            }

            if ($nombre && $email && $password && strlen($password) >= 6) {
                // Llama al Modelo pasando el rol como el último parámetro
                if ($this->usuarioModelo->registrarUsuario($nombre, $email, $password, $telefono, $rol_a_registrar)) {
                    header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&m=' . urlencode('Registro exitoso. Ahora puede iniciar sesión.'));
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