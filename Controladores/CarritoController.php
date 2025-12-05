<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/CarritoController.php
 * Propósito: Maneja acciones directas sobre el carrito (eliminar, actualizar cantidad).
 */

// Sube dos niveles: /Controladores/ -> /Raiz/ -> /Modelos/Carrito.php
require_once __DIR__ . '/../Modelos/Carrito.php';

class CarritoController {
    private $carritoModelo;

    public function __construct() {
        $this->carritoModelo = new Carrito();
    }

    /**
     * Elimina un ítem específico del carrito por su índice de array (clave).
     * URL: index.php?c=carrito&a=eliminaritem&id=X
     */
    public function eliminaritem($id) {
        $item_key = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $carrito = $this->carritoModelo->obtenerItems();
        
        $mensaje = null;
        
        // Verifica si la clave existe y la elimina de la sesión
        if (isset($carrito[$item_key])) {
            unset($carrito[$item_key]);
            $_SESSION['carrito'] = $carrito; // Reasignar el carrito sin el ítem
            $mensaje = "Producto eliminado del carrito.";
        } else {
            $mensaje = "Error: El producto no se encontró en el carrito.";
        }

        // Redirige de vuelta a la vista del carrito
        header('Location: ' . BASE_URL . 'index.php?c=cliente&a=carrito&mensaje=' . urlencode($mensaje));
        exit;
    }

    /**
     * [OPCIONAL] Manejaría la actualización de la cantidad (requiere formulario POST).
     * URL: index.php?c=carrito&a=actualizar
     */
    // public function actualizar() {
    //     // Lógica para procesar el POST y actualizar la cantidad de un ítem
    // }

    // NOTA: La acción "agregar" se mantiene en ClienteController para simplificar el flujo POST del catálogo.
}