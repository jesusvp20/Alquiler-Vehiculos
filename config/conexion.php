<?php
// Conexión a la base de datos usando pg_connect
class Conexion {
    // Propiedades de la clase
    private $host = "localhost";
    private $username = "postgres";
    private $password = "123456";
    private $db = "Alquiler-Vehiculos";
    private $connection;

    // Constructor de la clase
    public function __construct() {
        // Establecer la conexión
        $this->connection = pg_connect("host=$this->host dbname=$this->db user=$this->username password=$this->password");

        // Verificar si la conexión fue exitosa
        if (!$this->connection) {
            die("Error en la conexión con la base de datos");
        } else {
            echo "Conexión exitosa";
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
?>