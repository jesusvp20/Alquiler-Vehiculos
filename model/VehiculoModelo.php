<?php
require_once __DIR__ . '/../config/conexion.php';

class VehiculoModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Conexion())->getConnection();
    }

    /**
     * Obtener todos los vehículos
     */
    public function getAll() {
        $query = "SELECT * FROM vehiculos";
        $result = pg_query($this->conn, $query);
        return pg_fetch_all($result);
    }
    public function getByPlaca($placa) {
        $query = "SELECT v.*, t.nombre_tipo 
                  FROM vehiculos v 
                  JOIN tipovehiculo t ON v.id_tipo = t.id_tipo 
                  WHERE v.placa = $1";
    
        // Ejecutar la consulta con el parámetro
        $result = pg_query_params($this->conn, $query, [$placa]);
    
        // Retornar el resultado en forma de array asociativo
        return pg_fetch_assoc($result);
    }
    
    /**
     * Obtener un vehículo por su placa
     */
    public function getByTipo($id_tipo) {
        $query = "SELECT * FROM vehiculos WHERE id_tipo = $1";
    
        $result = pg_query_params($this->conn, $query, [$id_tipo]);
    
        return pg_fetch_all($result);
    }

    /**
     * Crear un nuevo vehículo
     */
    public function create($data) {
        $query = "INSERT INTO vehiculos (placa, numero_vehiculo, modelo_vehiculo, id_tipo, precio_alquiler, estado, imagen_vehiculo) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7)";

        $result = pg_query_params($this->conn, $query, [
            $data['placa'],
            $data['numero_vehiculo'],
            $data['modelo_vehiculo'],
            $data['id_tipo'],
            $data['precio_alquiler'],
            $data['estado'],
            $data['imagen_vehiculo'] ?? null
        ]);

        if ($result) {
            return ["success" => "Vehículo registrado correctamente"];
        } else {
            return ["error" => "Error al registrar vehículo: " . pg_last_error($this->conn)];
        }
    }

    /**
     * Actualizar los datos de un vehículo por su placa
     */
    public function update($placa, $data) {
        if (empty($placa)) {
            return ["error" => "La placa es obligatoria para actualizar el vehículo."];
        }

        $query = "UPDATE vehiculos 
                  SET numero_vehiculo = $1, 
                      modelo_vehiculo = $2, 
                      id_tipo = $3, 
                      precio_alquiler = $4, 
                      estado = $5, 
                      imagen_vehiculo = $6 
                  WHERE placa = $7";

        $result = pg_query_params($this->conn, $query, [
            $data['numero_vehiculo'],
            $data['modelo_vehiculo'],
            $data['id_tipo'],
            $data['precio_alquiler'],
            $data['estado'],
            $data['imagen_vehiculo'] ?? null,
            $placa
        ]);

        if ($result) {
            return ["success" => "Vehículo actualizado correctamente"];
        } else {
            return ["error" => "Error al actualizar vehículo: " . pg_last_error($this->conn)];
        }
    }

    /**
     * Eliminar un vehículo por su placa (y sus alquileres asociados)
     */
    public function delete($placa) {
        // Eliminar alquileres asociados
        $query_alquiler = "DELETE FROM alquiler WHERE placa_vehiculo = $1";
        $result_alquiler = pg_query_params($this->conn, $query_alquiler, [$placa]);

        if (!$result_alquiler) {
            return ["error" => "Error al eliminar alquileres: " . pg_last_error($this->conn)];
        }

        // Eliminar el vehículo
        $query_vehiculo = "DELETE FROM vehiculos WHERE placa = $1";
        $result_vehiculo = pg_query_params($this->conn, $query_vehiculo, [$placa]);

        if ($result_vehiculo) {
            return ["success" => "Vehículo eliminado correctamente"];
        } else {
            return ["error" => "Error al eliminar vehículo: " . pg_last_error($this->conn)];
            
        }
    }
}
?>
