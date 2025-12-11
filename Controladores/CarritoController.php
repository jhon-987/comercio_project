<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/CarritoController.php
 * CORREGIDO Y FINAL: Maneja la actualización de cantidad con stock.
 */

require_once __DIR__ . '/../Modelos/Carrito.php';

class CarritoController {
    private $carritoModelo;

    public function __construct() {
        $this->carritoModelo = new Carrito();
    }

    // ==========================================================
    // --- eliminaritem ---
    // ==========================================================
    public function eliminaritem($id) {

        $item_key = filter_var($id, FILTER_SANITIZE_NUMBER_INT); 
        
        $mensaje = null;
        
        if ($this->carritoModelo->eliminarItemPorKey($item_key)) {
            $mensaje = "Producto eliminado del carrito.";
        } else {
            $mensaje = "Error: El producto no se encontró o no pudo ser eliminado.";
        }

        // Apunta a la vista real del carrito
        header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&mensaje=' . urlencode($mensaje));
        exit;
    }

    // ==========================================================
    // --- actualizar cantidad
    // ==========================================================
    public function actualizar() {
        // 1. Verificar que la solicitud sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito');
            exit;
        }

        $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_SANITIZE_NUMBER_INT);
        $nueva_cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_SANITIZE_NUMBER_INT);

        // 🛑 NOTA: La depuración en CarritoController se ha dejado para ser más limpio.
        // Si hay problemas, el log debe hacerse dentro de actualizarCantidad del modelo.

        $mensaje = null;
        $error = null;

        if ($producto_id && $nueva_cantidad !== false) {
            
            // Si la cantidad es <= 0, el modelo Carrito maneja la eliminación
            if ($this->carritoModelo->actualizarCantidad($producto_id, $nueva_cantidad)) {
                
                if ($nueva_cantidad <= 0) {
                     $mensaje = 'Producto eliminado del carrito.';
                } else {
                     $mensaje = 'Cantidad actualizada correctamente.';
                }
            } else {
                // Esto ocurre si actualizarCantidad devuelve FALSE (Stock insuficiente)
                $error = 'Error al actualizar la cantidad o stock insuficiente.';
            }

        } else {
            $error = 'Datos incompletos o inválidos (ID o Cantidad nulos).';
        }

        $redirect_url = BASE_URL . 'index.php?c=cliente&a=carrito';
        if ($mensaje) {
            $redirect_url .= '&mensaje=' . urlencode($mensaje);
        } else if ($error) {
            $redirect_url .= '&error=' . urlencode($error);
        }
        
        header('Location: ' . $redirect_url);
        exit;
    }
}