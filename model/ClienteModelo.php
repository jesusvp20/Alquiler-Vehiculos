<?php
require_once __DIR__ . '/../config/conexion.php';
 class ClienteModelo{
     private $conn;public function __construct(){
        $this->conn = (new Conexion())->getConnection();
     }
     public function getALL(){
        $query = "SELECT * FROM cliente"; 
        $result = pg_query($this->conn, $query);
        return pg_fetch_all($result);
     }

     public function getById($id = null) {
        if ($id) {
            $sql = "SELECT * FROM \"cliente\" WHERE id = $1";
            $result = pg_query_params($this->conn, $sql, [$id]);
        } else {
            $sql = "SELECT * FROM \"cliente\"";
            $result = pg_query($this->conn, $sql);
        }
    
        if (!$result) {
            return ['error' => 'Error al ejecutar la consulta'];
        }
    
        return pg_fetch_all($result) ?: [];
    }
    public function create() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset($data['nombre_cliente'], $data['numero_documento'])) {
            sendError(400, "Faltan datos obligatorios");
            return;
        }
    
        $sql = "INSERT INTO cliente (nombre_cliente, numero_documento, telefono, email, fecha_registro) 
                VALUES ($1, $2, $3, $4, $5)";
    
        $result = pg_query_params($this->conn, $sql, [
            $data['nombre_cliente'],
            $data['numero_documento'],
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['fecha_registro'] ?? date('Y-m-d')
        ]);
    
        if ($result) {
            sendResponse(201, ["message" => "Cliente registrado correctamente"]);
        } else {
            sendError(500, pg_last_error($this->conn));
        }
    }
    public function update($id) {
        if (!$id) {
            sendError(400, "No se ha especificado el ID del cliente en la URL");
            return;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset($data['nombre_cliente'], $data['numero_documento'])) {
            sendError(400, "Faltan datos obligatorios para actualizar el cliente");
            return;
        }
    
        $sql = "UPDATE cliente 
                SET nombre_cliente = $1, 
                    numero_documento = $2, 
                    telefono = $3, 
                    email = $4, 
                    fecha_registro = $5
                WHERE id = $6";
    

    $result = pg_query_params($this->conn, $sql, [
            $data['nombre_cliente'],
            $data['numero_documento'],
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['fecha_registro'] ?? date('Y-m-d'),
            $id
        ]);
    
        if ($result) {
            $affected = pg_affected_rows($result);
            if ($affected > 0) {
                sendResponse(200, ["mensaje" => "Cliente actualizado correctamente"]);
            } else {
                sendResponse(200, ["mensaje" => "No se encontró cliente con ese ID o no se realizaron cambios"]);
            }
        } else {
            sendError(500, pg_last_error($this->conn));
        }
    }
    public function delete($id) {
        if (!$id) {
            sendError(400, "El campo 'id' es obligatorio.");
            return;
        }
    
        pg_query($this->conn, "BEGIN");
    
        $sql_delete_alquileres = "DELETE FROM Alquiler WHERE id_cliente = $1";
        $result_alquiler = pg_query_params($this->conn, $sql_delete_alquileres, [$id]);
    
        if (!$result_alquiler) {
            pg_query($this->conn, "ROLLBACK");
            sendError(500, "Error al eliminar alquileres: " . pg_last_error($this->conn));
            return;
        }
    
        $sql_delete_cliente = "DELETE FROM Cliente WHERE id = $1";
        $result_cliente = pg_query_params($this->conn, $sql_delete_cliente, [$id]);
    
        if (!$result_cliente) {
            pg_query($this->conn, "ROLLBACK");
            sendError(500, "Error al eliminar cliente: " . pg_last_error($this->conn));
            return;
        }
    
        pg_query($this->conn, "COMMIT");
        sendResponse(200, ["success" => "Cliente eliminado correctamente"]);
    }
      
    }

?>