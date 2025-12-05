<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/ProductoController.php
 */
require_once __DIR__ . '/../Modelos/Producto.php'; 

class ProductoController {
    private $productoModelo;

    public function __construct() {
        $this->productoModelo = new Producto();
    }

    public function verproducto($id = null) {
        $mensaje = $_GET['mensaje'] ?? null;
        $error = $_GET['error'] ?? null;
        
        // Lógica de eliminación
        $eliminar_id = filter_input(INPUT_GET, 'eliminar_id', FILTER_SANITIZE_NUMBER_INT);
        if ($eliminar_id) {
            if ($this->productoModelo->eliminarProducto($eliminar_id)) {
                $mensaje = "Producto eliminado exitosamente.";
            } else {
                $error = "Error al eliminar el producto. Podría estar asociado a restricciones.";
            }
            header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=' . urlencode($mensaje ?? $error));
            exit;
        }

        $productos = $this->productoModelo->obtenerTodos(); 
        require_once __DIR__ . '/../Vistas/admin/productos/verProducto.php';
    }

    public function crearproducto($id = null) {
        $error = null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $imagen = $_FILES['imagen']['name'] ?? 'default.jpg';

            if ($nombre && $precio !== false && $stock !== false) {
                $ruta_destino = __DIR__ . '/../../public/img/' . $imagen;
                if (!empty($_FILES['imagen']['tmp_name'])) {
                    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino);
                }

                if ($this->productoModelo->crearProducto($nombre, $descripcion, $precio, $stock, $imagen)) {
                    header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=Producto creado exitosamente.');
                    exit;
                } else {
                    $error = "Error de base de datos al crear el producto.";
                }
            } else {
                $error = "Datos inválidos. Verifique el precio y el stock.";
            }
        }
        require_once __DIR__ . '/../Vistas/admin/productos/crearProducto.php';
    }

    public function editarproducto($id) {
        $error = null;
        $producto_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        if (!$producto_id) {
            header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&error=ID no especificado.');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
            $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
            $imagen_actual = filter_input(INPUT_POST, 'imagen_actual', FILTER_SANITIZE_STRING);
            $imagen = $imagen_actual;

            if (!empty($_FILES['imagen_nueva']['name'])) {
                $imagen = $_FILES['imagen_nueva']['name'];
                $ruta_destino = __DIR__ . '/../../public/img/' . $imagen;
                move_uploaded_file($_FILES['imagen_nueva']['tmp_name'], $ruta_destino);
            }

            if ($nombre && $precio !== false && $stock !== false) {
                if ($this->productoModelo->actualizarProducto($producto_id, $nombre, $descripcion, $precio, $stock, $imagen)) {
                    header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&mensaje=Producto actualizado exitosamente.');
                    exit;
                } else {
                    $error = "Error de base de datos al actualizar el producto.";
                }
            } else {
                $error = "Datos inválidos. Verifique el precio y el stock.";
            }
        }
        
        $producto = $this->productoModelo->obtenerPorId($producto_id);
        if (!$producto) {
            header('Location: ' . BASE_URL . 'index.php?c=producto&a=verproducto&error=Producto no encontrado.');
            exit;
        }
        require_once __DIR__ . '/../Vistas/admin/productos/editarProducto.php';
    }
}