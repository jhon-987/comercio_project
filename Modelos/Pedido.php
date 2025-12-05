<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Modelos/Pedido.php
 * Final: Gestiona la creación transaccional y el CRUD de pedidos.
 */
require_once __DIR__ . '/../Config/conexion.php'; 

class Pedido {
    private $conn;
    private $table_pedidos = "pedidos";
    private $table_detalle = "detalle_pedido";
    private $table_productos = "productos";

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
    }

    // --- CREACIÓN Y TRANSACCIÓN ---
    public function crearPedido($usuario_id, $carrito, $metodo_pago, $direccion) {
        if (!$this->conn || empty($carrito)) return false;

        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        try {
            $this->conn->beginTransaction();

            // 1. Insertar en la tabla 'pedidos'
            $query_pedido = "INSERT INTO " . $this->table_pedidos . " (usuario_id, total, metodo_pago) 
                             VALUES (:usuario_id, :total, :metodo_pago)";
            $stmt_pedido = $this->conn->prepare($query_pedido);
            $stmt_pedido->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_pedido->bindParam(':total', $total);
            $stmt_pedido->bindParam(':metodo_pago', $metodo_pago);
            $stmt_pedido->execute();
            
            $pedido_id = $this->conn->lastInsertId();

            // 2. Insertar en 'detalle_pedido' y reducir stock
            $query_stock = "UPDATE " . $this->table_productos . " SET stock = stock - :cantidad WHERE id = :producto_id AND stock >= :cantidad";
            $query_detalle = "INSERT INTO " . $this->table_detalle . " (pedido_id, producto_id, cantidad, precio_unitario) 
                              VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario)";
                                  
            foreach ($carrito as $item) {
                // Reducir stock
                $stmt_stock = $this->conn->prepare($query_stock);
                $stmt_stock->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmt_stock->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmt_stock->execute();

                if ($stmt_stock->rowCount() === 0) {
                     throw new Exception("Stock insuficiente para el producto ID: " . $item['id']);
                }

                // Insertar detalle
                $stmt_detalle = $this->conn->prepare($query_detalle);
                $stmt_detalle->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
                $stmt_detalle->bindParam(':producto_id', $item['id'], PDO::PARAM_INT);
                $stmt_detalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmt_detalle->bindParam(':precio_unitario', $item['precio']);
                $stmt_detalle->execute();
            }

            $this->conn->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    // --- LECTURA Y ADMINISTRACIÓN ---
    public function obtenerTotalPorId($pedido_id) {
        if (!$this->conn) return 0;
        $query = "SELECT total FROM " . $this->table_pedidos . " WHERE id = :pedido_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public function obtenerTodosPedidosAdmin() {
        if (!$this->conn) return [];
        $query = "SELECT p.id, p.fecha_pedido, p.estado, p.total, u.nombre AS nombre_cliente 
                  FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id ORDER BY p.fecha_pedido DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoPedido($pedido_id, $nuevo_estado) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_pedidos . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $nuevo_estado);
        $stmt->bindParam(':id', $pedido_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerDetallePedido($pedido_id) {
        if (!$this->conn) return false;
        $query = "SELECT p.*, u.nombre AS nombre_cliente, u.email FROM pedidos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) return false;

        $query_items = "SELECT dp.*, prod.nombre AS nombre_producto, prod.precio AS precio_catalogo 
                        FROM detalle_pedido dp JOIN productos prod ON dp.producto_id = prod.id WHERE dp.pedido_id = :id";
        
        $stmt_items = $this->conn->prepare($query_items);
        $stmt_items->bindParam(':id', $pedido_id, PDO::PARAM_INT);
        $stmt_items->execute();
        $pedido['items'] = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        return $pedido;
    }

    public function eliminarPedido($pedido_id) {
        if (!$this->conn) return false;
        try {
            $this->conn->beginTransaction();
            $query_detalle = "DELETE FROM " . $this->table_detalle . " WHERE pedido_id = :id";
            $stmt_detalle = $this->conn->prepare($query_detalle);
            $stmt_detalle->bindParam(':id', $pedido_id, PDO::PARAM_INT);
            $stmt_detalle->execute();

            $query_pedido = "DELETE FROM " . $this->table_pedidos . " WHERE id = :id";
            $stmt_pedido = $this->conn->prepare($query_pedido);
            $stmt_pedido->bindParam(':id', $pedido_id, PDO::PARAM_INT);
            $stmt_pedido->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}