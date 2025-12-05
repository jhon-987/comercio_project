<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Modelos/Producto.php
 * Final: Gestiona el CRUD completo de la tabla 'productos'.
 */
require_once __DIR__ . '/../Config/conexion.php'; 

class Producto {
    private $conn;
    private $table_name = "productos";

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    // --- MÉTODOS DE LECTURA ---
    public function obtenerTodos() {
        if (!$this->conn) return [];
        $query = "SELECT id, nombre, descripcion, precio, stock, imagen FROM " . $this->table_name . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        if (!$this->conn) return false;
        $query = "SELECT id, nombre, descripcion, precio, stock, imagen FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // --- MÉTODOS CRUD ADMIN ---
    public function crearProducto($nombre, $descripcion, $precio, $stock, $imagen) {
        if (!$this->conn) return false;
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, precio, stock, imagen) 
                  VALUES (:nombre, :descripcion, :precio, :stock, :imagen)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagen);
        return $stmt->execute();
    }
    
    public function actualizarProducto($id, $nombre, $descripcion, $precio, $stock, $imagen) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen = :imagen
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function eliminarProducto($id) {
        if (!$this->conn) return false;
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}