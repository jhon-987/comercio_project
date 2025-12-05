<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Modelos/Carrito.php
 */
class Carrito {

    private function ensureInitialized() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
    }

    public function agregarItem($idProducto, $nombre, $precio, $cantidad = 1) {
        $this->ensureInitialized();
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $idProducto) {
                $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
                return true;
            }
        }
        $_SESSION['carrito'][] = [
            'id' => $idProducto,
            'nombre' => $nombre,
            'precio' => (float)$precio,
            'cantidad' => (int)$cantidad,
        ];
        return true;
    }

    public function obtenerItems() {
        $this->ensureInitialized();
        return $_SESSION['carrito'];
    }

    public function vaciar() {
        $_SESSION['carrito'] = [];
    }
    
    public function eliminarItemPorKey($key) {
        $this->ensureInitialized();
        if (isset($_SESSION['carrito'][$key])) {
            unset($_SESSION['carrito'][$key]);
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