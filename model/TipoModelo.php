<?php
require_once __DIR__ . '/../config/conexion.php';

class TipoModelo {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM tipovehiculo";
        $result = pg_query($this->conn, $query);
        return pg_fetch_all($result);
    }

    public function getById($id_tipo) {
        $query = "SELECT * FROM tipovehiculo WHERE id_tipo = $1";
        $result = pg_query_params($this->conn, $query, [$id_tipo]);
        return pg_fetch_assoc($result);
    }

    public function create($data) {
        if (!isset($data['nombre_tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre_tipo' es obligatorio"]);
            exit();
        }

        $query = "INSERT INTO tipovehiculo (nombre_tipo) VALUES ($1)";
        $result = pg_query_params($this->conn, $query, [$data['nombre_tipo']]);

        if ($result) {
            sendResponse(200, ['message' => 'Vehículo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar vehículo');
        }
    }

    public function update($id_tipo, $data) {
        if (!isset($data['nombre_tipo'])) {
            http_response_code(400);
            echo json_encode(["error" => "El campo 'nombre_tipo' es obligatorio"]);
            exit();
        }

        $query = "UPDATE tipovehiculo SET nombre_tipo = $1 WHERE id_tipo = $2";
        $result = pg_query_params($this->conn, $query, [$data['nombre_tipo'], $id_tipo]);

        if ($result) {
            sendResponse(200, ['message' => 'Vehículo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar vehículo');
        }
    }

    public function delete($id_tipo) {

        $query_check = "SELECT COUNT(*) FROM vehiculos WHERE id_tipo = $1";
        $result_check = pg_query_params($this->conn, $query_check, [$id_tipo]);
        $count = pg_fetch_result($result_check, 0, 0);

        if ($count > 0) {
            http_response_code(409);
            echo json_encode(["error" => "No se puede eliminar. Existen vehículos asociados a este tipo."]);
            exit();
        }

        $query_delete = "DELETE FROM tipovehiculo WHERE id_tipo = $1";
        $result = pg_query_params($this->conn, $query_delete, [$id_tipo]);

        if ($result) {
            sendResponse(200, ['message' => 'Vehículo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar vehículo');
        }
    }
}