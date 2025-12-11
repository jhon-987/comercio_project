<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/ProductoController.php
 * Implementa la lógica de subida de imagen en crear().
 */
require_once __DIR__ . '/../Modelos/Producto.php'; 

class ProductoController {
    private $productoModelo;

    public function __construct() {
        $this->productoModelo = new Producto();
    }

    private function checkAdminAccess() {
        if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'empleado')) {
            header('Location: ' . BASE_URL . 'index.php?c=auth&a=login&error=' . urlencode('Acceso denegado. Requiere permisos de administración.'));
            exit;
        }
    }

    // ==========================================================
    // --- CREACIÓN (Con Lógica de Subida de Imagen) ---
    // ==========================================================
    public function crear() {
        $this->checkAdminAccess();

        $error = null;
        $categorias = $this->productoModelo->obtenerCategorias(); 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_NUMBER_INT); 
            
            // 🛑 1. Valor por defecto para la URL de imagen (si no hay subida)
            // Esto activa el Placeholder en el catálogo.
            $imagen_url_db = 'url_imagen/placeholder_default.jpg'; 

            // 2. Lógica de Subida de Imagen (Si se subió un archivo)
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                
                // Definir el directorio de destino y crear un nombre único
                $upload_dir = __DIR__ . '/../../public/img/'; // Ruta absoluta al destino: [Raiz]/public/img/
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid('prod_') . '_' . time() . '.' . $extension; // Nombre único y seguro
                $target_file = $upload_dir . $file_name;

                // Mover el archivo subido de la carpeta temporal a la carpeta de imágenes
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                    // Guardar SOLO el nombre del archivo en la DB
                    $imagen_url_db = $file_name; 
                } else {
                    $error = "Error al mover el archivo subido. Verifique permisos de la carpeta 'public/img/'.";
                }
            } else if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Manejar errores de subida como tamaño excedido
                $error = "Error de subida de archivo: Código " . $_FILES['imagen']['error'];
            }
            // FIN Lógica de Subida

            // 3. Validación y Creación del Producto
            if (!$error && $nombre && $precio !== false && $stock !== false && $categoria_id) {
                
                if ($this->productoModelo->crearProducto($nombre, $descripcion, $precio, $stock, $imagen_url_db, $categoria_id)) {
                    header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=' . urlencode('Producto creado exitosamente.'));
                    exit;
                } else {
                    $error = "Error de base de datos al crear el producto.";
                }
            } else if (!$error) {
                $error = "Datos inválidos. Verifique todos los campos.";
            }
        }
        
        require_once __DIR__ . '/../Vistas/admin/productos/crearProducto.php';
    }

    // ... (El resto de las funciones verproducto, editar, eliminar se mantienen) ...
    public function verproducto() { 
        $this->checkAdminAccess();
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        $productos = $this->productoModelo->obtenerTodos(); 
        require_once __DIR__ . '/../Vistas/admin/productos/verProducto.php'; 
    }
    
    public function editar($id) {
        $this->checkAdminAccess();
        $error = null;
        $producto_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $categorias = $this->productoModelo->obtenerCategorias(); 
        
        if (!$producto_id) {
            header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&error=' . urlencode('ID no especificado.'));
            exit;
        }

        $producto = $this->productoModelo->obtenerPorId($producto_id);
        $imagen_actual = $producto['imagen_url'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_NUMBER_INT); 
            
            $imagen_url_db = $imagen_actual; // Mantener la imagen actual por defecto

            // Lógica de actualización de imagen (Si se subió un nuevo archivo)
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../../public/img/';
                $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid('prod_') . '_' . time() . '.' . $extension;
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                    // Opcional: Eliminar la imagen antigua del disco si no es el placeholder
                    // if ($imagen_actual != 'url_imagen/placeholder_default.jpg' && file_exists($upload_dir . $imagen_actual)) { unlink($upload_dir . $imagen_actual); }
                    $imagen_url_db = $file_name;
                } else {
                    $error = "Error al subir el nuevo archivo de imagen.";
                }
            }
            
            if (!$error && $nombre && $precio !== false && $stock !== false && $categoria_id) {
                 if ($this->productoModelo->actualizarProducto($producto_id, $nombre, $descripcion, $precio, $stock, $imagen_url_db, $categoria_id)) {
                     header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=' . urlencode('Producto actualizado exitosamente.'));
                     exit;
                 } else {
                     $error = "Error de base de datos al actualizar el producto.";
                 }
            } else if (!$error) {
                $error = "Datos inválidos. Verifique el precio, stock y la categoría.";
            }
        }
        
        if (!$producto) {
            header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&error=' . urlencode('Producto no encontrado.'));
            exit;
        }
        require_once __DIR__ . '/../Vistas/admin/productos/editarProducto.php';
    }

    public function eliminar() {
        $this->checkAdminAccess();
        
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $mensaje = null;

        if ($id) {
            // Opcional: Recuperar el nombre del archivo y eliminarlo del disco antes de borrar el registro
            if ($this->productoModelo->eliminarProducto($id)) {
                $mensaje = "Producto eliminado exitosamente.";
            } else {
                $mensaje = "Error al eliminar el producto. Podría estar asociado a restricciones de integridad (ej: pedidos).";
            }
        } else {
            $mensaje = 'ID de producto no válido.';
        }
        
        header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=' . urlencode($mensaje));
        exit;
    }
}