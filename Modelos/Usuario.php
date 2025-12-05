<?php
/**
 * Archivo: COMERCIO_PROJECT-MASTER/Modelos/Usuario.php
 * FINAL: Gestiona la tabla 'usuarios' para Auth, Perfil, y CRUD.
 * Incluye la lógica de 'seeder' para autocrear el usuario administrador.
 */
require_once __DIR__ . '/../Config/conexion.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";
    
    // Definiciones del administrador para el seeder
    private $admin_email = 'admin@example.com';
    private $admin_password_plana = 'admin';

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->conectar();
        
        // Ejecuta la función para garantizar que el admin exista.
        $this->seedAdminUser(); 
    }
    
    /**
     * Verifica si el usuario administrador existe y lo crea si no existe.
     */
    public function seedAdminUser() {
        if (!$this->conn) return false;

        // 1. Verificar si el usuario administrador ya existe
        $query_check = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':email', $this->admin_email);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            return true;
        }

        // 2. Crear el hash dinámicamente con PHP
        $password_hash = password_hash($this->admin_password_plana, PASSWORD_DEFAULT);

        $query_insert = "INSERT INTO " . $this->table_name . " (nombre, email, password_hash, rol) 
                         VALUES ('Admin User', :email, :password_hash, 'admin')";
        
        $stmt_insert = $this->conn->prepare($query_insert);
        $stmt_insert->bindParam(':email', $this->admin_email);
        $stmt_insert->bindParam(':password_hash', $password_hash);
        
        return $stmt_insert->execute();
    }


    // --- AUTENTICACIÓN Y REGISTRO ---
    public function validarLogin($email, $password) {
        if (!$this->conn) return false;
        
        $query = "SELECT id, nombre, email, password_hash, rol FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            unset($usuario['password_hash']);
            return $usuario;
        }
        return false;
    }

    /**
     * [MEJORA IMPLEMENTADA] Registra un nuevo usuario con rol dinámico.
     */
    public function registrarUsuario($nombre, $email, $password, $telefono = null, $rol = 'cliente') {
        if (!$this->conn) return false;
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // La consulta ahora usa el parámetro :rol, no 'cliente' fijo
        $query = "INSERT INTO " . $this->table_name . " (nombre, email, password_hash, telefono, rol) 
                  VALUES (:nombre, :email, :password_hash, :telefono, :rol)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':rol', $rol); // Asigna el rol pasado ('cliente' o 'admin')
        
        return $stmt->execute();
    }
    
    // --- LECTURA DE DATOS ---
    public function obtenerPorId($id) {
        if (!$this->conn) return false;
        $query = "SELECT id, nombre, email, rol, direccion, telefono, fecha_registro FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerTodos() { 
        if (!$this->conn) return [];
        $query = "SELECT id, nombre, email, rol, telefono, fecha_registro FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerClientes() { 
        if (!$this->conn) return [];
        $query = "SELECT id, nombre, email, telefono FROM " . $this->table_name . " WHERE rol = 'cliente' ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // --- MÉTODOS DE ACTUALIZACIÓN Y CRUD ADMIN ---
    public function actualizarPerfil($usuario_id, $nombre, $telefono, $direccion) {
        if (!$this->conn) return false;
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, telefono = :telefono, direccion = :direccion WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function actualizarPassword($usuario_id, $actual_password, $nueva_password) {
        if (!$this->conn) return null;
        $query = "SELECT password_hash FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $hash_actual = $stmt->fetchColumn();

        if ($hash_actual && password_verify($actual_password, $hash_actual)) {
            $nuevo_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $query_update = "UPDATE " . $this->table_name . " SET password_hash = :nuevo_hash WHERE id = :id";
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bindParam(':nuevo_hash', $nuevo_hash);
            $stmt_update->bindParam(':id', $usuario_id, PDO::PARAM_INT);
            return $stmt_update->execute();
        }
        return false;
    }
    
    public function eliminarUsuario($id) {
        if (!$this->conn) return false;
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}