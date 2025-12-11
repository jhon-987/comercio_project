<?php
/**
 * COMERCIO_PROJECT-MASTER/Modelos/Producto.php
 * FINAL: Elimina la restricción 'activo=TRUE' de la verificación de stock.
 */
require_once __DIR__ . '/../Config/conexion.php'; 

class Producto {
    private $conn;
    private $table_name = "productos";
    private $categories_table = "categorias";

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    // ==========================================================
    // --- OBTENER STOCK (CORREGIDO: Filtro 'activo' eliminado) ---
    // ==========================================================
    public function obtenerStock($productoId) {
        if (!$this->conn) return 0;
        
        $query = "SELECT stock FROM " . $this->table_name . " WHERE id = :id LIMIT 1"; 
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $productoId, PDO::PARAM_INT);
        $stmt->execute();
        
        $stock = $stmt->fetchColumn(); 
        return (int)$stock; 
    }
    
    public function obtenerCategorias() {
        if (!$this->conn) return [];
        $query = "SELECT id, nombre FROM " . $this->categories_table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerConFiltros($search_term = null, $category_id = null) {
        if (!$this->conn) return [];
        
        $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen_url, c.nombre AS categoria_nombre 
                  FROM " . $this->table_name . " p
                  LEFT JOIN " . $this->categories_table . " c ON p.categoria_id = c.id
                  WHERE p.activo = TRUE"; 

        // ... (resto de la lógica de filtros) ...
        $params = [];
        if (!empty($search_term)) {
            $query .= " AND (p.nombre LIKE :search_term OR p.descripcion LIKE :search_term_desc)";
            $params[':search_term'] = "%" . $search_term . "%";
            $params[':search_term_desc'] = "%" . $search_term . "%";
        }
        if ($category_id > 0) {
            $query .= " AND p.categoria_id = :category_id";
            $params[':category_id'] = (int)$category_id;
        }
        $query .= " ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => &$val) {
            if ($key === ':category_id') {
                $stmt->bindParam($key, $val, PDO::PARAM_INT);
            } else {
                $stmt->bindParam($key, $val, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerTodos() {
        if (!$this->conn) return [];
        $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen_url, c.nombre AS categoria_nombre 
                     FROM " . $this->table_name . " p
                     LEFT JOIN " . $this->categories_table . " c ON p.categoria_id = c.id
                     WHERE p.activo = TRUE 
                     ORDER BY p.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        if (!$this->conn) return false;
        $query = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen_url, p.categoria_id, c.nombre AS categoria_nombre 
                     FROM " . $this->table_name . " p
                     LEFT JOIN " . $this->categories_table . " c ON p.categoria_id = c.id
                     WHERE p.id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function crearProducto($nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id) {
        if (!$this->conn) return false;
        $query = "INSERT INTO " . $this->table_name . " (nombre, descripcion, precio, stock, imagen_url, categoria_id, activo) 
                  VALUES (:nombre, :descripcion, :precio, :stock, :imagen_url, :categoria_id, TRUE)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':imagen_url', $imagen_url); 
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT); 
        return $stmt->execute();
    }
    
    public function actualizarProducto($id, $nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, imagen_url = :imagen_url, categoria_id = :categoria_id
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
        $stmt->bindParam(':imagen_url', $imagen_url); 
        $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT); 
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