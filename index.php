<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/index.php
 * Final: Controlador Frontal Dinámico y Seguridad.
 */
session_start();

// Configuración de errores para desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Obtener controlador (c), acción (a), e ID
$controlador = $_GET['c'] ?? 'cliente';
$accion      = $_GET['a'] ?? 'catalogo';
$id          = $_GET['id'] ?? null; 

/*
 * BASE_URL: ruta base del proyecto.
 * Se calcula dinámicamente a partir de SCRIPT_NAME para soportar subdirectorios
 * (por ejemplo: '/comercio_project-master/'). Garantiza una barra final.
 */
$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($basePath === '/' || $basePath === '.') {
    $basePath = '/';
} else {
    $basePath = rtrim($basePath, '/') . '/';
}
define('BASE_URL', $basePath);

// 2.3. Determinar si el controlador requiere autenticación de administrador.
$es_modulo_admin = in_array(strtolower($controlador), ['admin', 'pedido', 'producto', 'usuario', 'empleado']);

// --- VERIFICACIÓN DE SEGURIDAD (ADMIN) ---
// Este bloque protege las rutas de CRUD de Pedidos, Productos, etc.
if ($es_modulo_admin) {
    // Si intenta acceder a un módulo de admin sin ser admin, redirigir al Login.
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
        header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=Acceso denegado. Debe iniciar sesión como administrador.');
        exit;
    }
}


// --- 3. PROCESAMIENTO DEL CONTROLADOR ---

// 3.1. Normalizar y definir la ruta
$nombre_controlador = ucfirst(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $controlador))) . 'Controller';
$archivo_controlador = __DIR__ . '/Controladores/' . $nombre_controlador . '.php';
$nombre_accion = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $accion));


if (file_exists($archivo_controlador)) {
    require_once $archivo_controlador;
    
    if (class_exists($nombre_controlador)) {
        $controllerInstance = new $nombre_controlador();
        
        if (method_exists($controllerInstance, $nombre_accion)) {
            $controllerInstance->$nombre_accion($id); 
        } else {
            http_response_code(404);
            echo "<h1>Error 404: Acción ('" . htmlspecialchars($accion) . "') no válida para el controlador " . htmlspecialchars($controlador) . ".</h1>";
        }
        
    } else {
        http_response_code(500);
        echo "<h1>Error 500: Clase " . htmlspecialchars($nombre_controlador) . " no encontrada.</h1>";
    }

} else {
    http_response_code(404);
    echo "<h1>Error 404: Controlador ('" . htmlspecialchars($controlador) . "') no encontrado.</h1>";
}