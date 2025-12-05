<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Controladores/AdminController.php
 */
class AdminController {
    
    public function dashboard($id = null) {
        // NOTA: La verificación de rol de administrador ocurre en index.php
        $titulo = "Panel de Administración";
        
        require_once __DIR__ . '/../Vistas/admin/dashboard.php';
    }
}