<?php
require_once __DIR__ . '/../config/conexion.php';
class AlquilerModelo{
    
    private $conn;
    public function __construct(){
        $this->conn = (new Conexion())->getConnection();
     }
    
     public function getALL() {
        $query = "SELECT * FROM alquiler";
        $result = pg_query($this->conn, $query); // Usa pg_query() en lugar de pg_query_params()
        
        if (!$result) {
            return []; // Retorna un array vacío si hay un error
        }
    
        return pg_fetch_all($result) ?: []; // Retorna los datos o un array vacío si no hay resultados
    }
    public function getById($id = null) {
        $sql = "SELECT a.*, c.nombre_cliente, v.modelo_vehiculo, v.placa 
                FROM \"alquiler\" a 
                JOIN \"cliente\" c ON a.id_cliente = c.id 
                JOIN \"vehiculos\" v ON a.placa_vehiculo = v.placa";
    
        if ($id) {
            $sql .= " WHERE a.id = $1";
            $result = pg_query_params($this->conn, $sql, [$id]);
        } else {
            $result = pg_query($this->conn, $sql);
        }
    
        if (!$result) {
            sendError(500, "Error en la consulta: " . pg_last_error($this->conn));
            return;
        }
    
        $data = pg_fetch_all($result);
    
        // Si no hay datos, devolvemos un mensaje adecuado
        if (!$data) {
            sendResponse(404, ["message" => "No se encontraron registros"]);
            return;
        }
    
        sendResponse(200, $data);
    }
    public function create() {
        // Obtener los datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Validar datos obligatorios
        if (!isset($data['detalles_alquiler'], $data['precio_alquiler'], $data['fecha_alquiler'], $data['fecha_devolucion'], $data['id_cliente'], $data['placa_vehiculo'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos obligatorios para registrar el alquiler"]);
            exit();
        }
    
        // Consulta SQL con parámetros
        $sql = "INSERT INTO alquiler (detalles_alquiler, precio_alquiler, fecha_alquiler, fecha_devolucion, id_cliente, placa_vehiculo) 
                VALUES ($1, $2, $3, $4, $5, $6)";
    
        // Ejecutar consulta con parámetros
        $result = pg_query_params($this->conn, $sql, [
            $data['detalles_alquiler'],
            $data['precio_alquiler'],
            $data['fecha_alquiler'],
            $data['fecha_devolucion'],
            $data['id_cliente'],
            $data['placa_vehiculo']
        ]);
    
        // Verificar resultado
        if ($result) {
            header("HTTP/1.1 201 Created");
            echo json_encode(["message" => "Alquiler registrado correctamente"]);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => pg_last_error($this->conn)]);
        }
    }
    
    public function update($id) {
        // Obtener los datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
    
        // Validar datos obligatorios
        if (!isset($data['detalles_alquiler'], $data['precio_alquiler'], $data['fecha_alquiler'], $data['fecha_devolucion'], $data['id_cliente'], $data['placa_vehiculo'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos obligatorios para actualizar el alquiler"]);
            exit();
        }
    
        // Consulta SQL con parámetros
        $sql = "UPDATE alquiler 
                SET detalles_alquiler = $1, 
                    precio_alquiler = $2,
                    fecha_alquiler = $3, 
                    fecha_devolucion = $4, 
                    id_cliente = $5, 
                    placa_vehiculo = $6
                WHERE id = $7";
    
        // Ejecutar la consulta con los parámetros
        $result = pg_query_params($this->conn, $sql, [
            $data['detalles_alquiler'],
            $data['precio_alquiler'],
            $data['fecha_alquiler'],
            $data['fecha_devolucion'],
            $data['id_cliente'],
            $data['placa_vehiculo'],
            $id
        ]);
    
        // Verificar resultado
        if ($result) {
            $affected = pg_affected_rows($result);
            if ($affected > 0) {
                header("HTTP/1.1 200 OK");
                echo json_encode(["mensaje" => "Alquiler actualizado correctamente"]);
            } else {
                header("HTTP/1.1 200 OK");
                echo json_encode(["mensaje" => "No se encontró alquiler con ese ID o no se realizaron cambios"]);
            }
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["error" => pg_last_error($this->conn)]);
        }
    }
    public function delete($id) {
        // Verificar si el ID es válido
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(["error" => "El ID del alquiler es obligatorio"]);
            exit();
        }
    
        // Consulta SQL para eliminar el alquiler
        $sql = "DELETE FROM alquiler WHERE id = $1";
        $result = pg_query_params($this->conn, $sql, [$id]);
    
        // Verificar resultado
        if ($result) {
            $affected = pg_affected_rows($result);
            if ($affected > 0) {
                http_response_code(200);
                echo json_encode(["success" => "Alquiler eliminado correctamente"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No se encontró alquiler con ese ID"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al eliminar alquiler: " . pg_last_error($this->conn)]);
        }
    }
    
}
?>