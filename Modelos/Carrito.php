<?php
/*
 * Archivo: COMERCIO_PROJECT-MASTER/Modelos/Carrito.php
 */

require_once __DIR__ . '/Producto.php'; 

class Carrito {
    private $productoModelo; 

    public function __construct() {
        $this->productoModelo = new Producto(); 
    }

    private function ensureInitialized() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    // =========================================================
    // --- FUNCIÓN AGREGAR ITEM ---
    // =========================================================
    public function agregarItem($idProducto, $nombre, $precio, $cantidad = 1) {
        $this->ensureInitialized();
        
        $idProducto = (int)$idProducto;
        $cantidad = (int)$cantidad;
        $stockDisponible = $this->productoModelo->obtenerStock($idProducto);

        $cantidadEnCarrito = 0;
        $itemKey = -1; 
        
        // 1. Buscar ítem existente para obtener la cantidad actual
        foreach ($_SESSION['carrito'] as $key => $item) {
            // 🛑 CORRECCIÓN: Castear a INT antes de la comparación estricta (===)
            if ((int)$item['id_producto'] === $idProducto) { 
                $cantidadEnCarrito = $item['cantidad'];
                $itemKey = $key;
                break;
            }
        }
        
        // 2. Verificar límite de stock
        $nuevaCantidadTotal = $cantidadEnCarrito + $cantidad;
        
        if ($nuevaCantidadTotal > $stockDisponible) {
            return "Stock insuficiente. Solo quedan " . ($stockDisponible - $cantidadEnCarrito) . " unidades disponibles.";
        }

        // 3. Si hay stock, actualizar o añadir
        if ($itemKey !== -1) {
            $_SESSION['carrito'][$itemKey]['cantidad'] = $nuevaCantidadTotal;
        } else {
            $_SESSION['carrito'][] = [
                'id_producto' => $idProducto,
                'nombre' => $nombre,
                'precio' => (float)$precio,
                'cantidad' => $cantidad,
            ];
        }
        return true; 
    }
    
    // =========================================================
    // --- FUNCIÓN ACTUALIZAR CANTIDAD ---
    // =========================================================
    public function actualizarCantidad($idProducto, $nuevaCantidad) {
        $this->ensureInitialized();

        $idProducto = (int)$idProducto;
        $nuevaCantidad = (int)$nuevaCantidad;

        if ($nuevaCantidad <= 0) {
            return $this->eliminarItemPorId($idProducto);
        }
        
        // 1. Obtener stock disponible de la base de datos (Usa Producto.php corregido)
        $stockDisponible = $this->productoModelo->obtenerStock($idProducto); 
        
        // 2. Verificar si la nueva cantidad excede el stock
        if ($nuevaCantidad > $stockDisponible) {
             return false; 
        }
        
        $found = false;
        
        // 3. Buscar y actualizar el producto en el carrito
        foreach ($_SESSION['carrito'] as $key => $item) {
            // 🛑 CORRECCIÓN: Castear a INT antes de la comparación estricta (===)
            if ((int)$item['id_producto'] === $idProducto) { 
                $_SESSION['carrito'][$key]['cantidad'] = $nuevaCantidad;
                $found = true;
                break;
            }
        }
        
        return $found; 
    }
    
    // =========================================================
    // --- OTRAS FUNCIONES ---
    // =========================================================
    public function obtenerItems() {
        $this->ensureInitialized();
        return $_SESSION['carrito'];
    }

    public function vaciar() {
        $_SESSION['carrito'] = [];
    }
    
    public function eliminarItemPorId($idProducto) {
        $this->ensureInitialized();
        $idProducto = (int)$idProducto; // Aseguramos que el ID sea INT
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ((int)$item['id_producto'] === $idProducto) { // 🛑 CORRECCIÓN: Comparación estricta con casteo
                unset($_SESSION['carrito'][$key]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']); 
                return true;
            }
        }
        return false;
    }

    public function eliminarItemPorKey($key) {
        $this->ensureInitialized();
        if (isset($_SESSION['carrito'][$key])) {
            unset($_SESSION['carrito'][$key]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); 
            return true;
        }
        return false;
    }

    public function calcularTotal() {
        $this->ensureInitialized();
        $total = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        return $total;
    }
}