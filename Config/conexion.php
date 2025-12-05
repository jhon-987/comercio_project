<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Config/conexion.php
 * Propósito: Clase para manejar la conexión a la base de datos (DB) usando PDO.
 */

class Conexion {
    private $host = 'localhost';
    private $db_name = 'comercio_db'; // ¡Asegúrate que este nombre sea correcto!
    private $username = 'root';      // ¡AJUSTA TU USUARIO!
    private $password = 'jhonpablo1';          // ¡AJUSTA TU CONTRASEÑA!
    private $conn;

    /**
     * Obtiene la conexión a la base de datos (PDO).
     * @return PDO|null Retorna el objeto de conexión PDO o null en caso de error.
     */
    public function conectar() {
        $this->conn = null;

        try {
            // Intentamos establecer la conexión con los parámetros definidos
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                // Opciones para manejo de errores y codificación UTF-8
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        } catch (PDOException $exception) {
            // Si hay un error, lo mostramos (solo en desarrollo) y detenemos el script
            echo "Error de conexión a la base de datos: " . $exception->getMessage();
            die(); 
        }

        return $this->conn;
    }
}