<?php

require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../model/AlquilerModelo.php';

class AlquilerController{
    private $model;
    
    public function __construct() {
        $this->model = new AlquilerModelo();
    }

    
    
    public function handleRequest($method, $id = null) {
        switch ($method) {
            case 'GET':
                $this->get($id);
                break;
            case 'POST':
                $this->post();
                break;
            case 'PUT':
                $this->put($id);
                break;
            case 'DELETE':
                $this->delete($id);
                break;
            default:
                sendError(405, 'Método no permitido');
                break;
        }
    }

    private function get($id) {
        if ($id) {
            $alquiler = $this->model->getById($id);
            if ($alquiler) {
                sendResponse(200, $alquiler);
            } else {
                sendError(404, 'Cliente no encontrado');
            }
        } else {
            $alquiler = $this->model->getAll();
            sendResponse(200, $alquiler);
        }
    }

    private function post() {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset(
            $data['detalles_alquiler'], 
            $data['precio_alquiler'], 
            $data['fecha_alquiler'], 
            $data['fecha_devolucion'], 
            $data['id_cliente'], 
            $data['placa_vehiculo']
        )) {
            sendError(400, "Faltan datos obligatorios para registrar el alquiler");
            exit();
        }
    
        if (strtotime($data['fecha_devolucion']) < strtotime($data['fecha_alquiler'])) {
            sendError(400, "La fecha de devolución no puede ser anterior a la fecha de alquiler");
            exit();
        }
    
        $result = $this->model->create($data);
    
        if ($result === true) { 
            sendResponse(201, ['message' => 'Alquiler creado correctamente']);
        } else {
            sendError(500, 'Error al crear el alquiler');
        }
    }
    

    private function put($id) {
        if (empty($id)) {
            sendError(400, 'El ID del cliente es obligatorio');
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['detalles_alquiler'], $data['precio_alquiler'], $data['fecha_alquiler'], $data['fecha_devolucion'], $data['id_cliente'], $data['placa_vehiculo'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos obligatorios para registrar el alquiler"]);
            exit();
        }

        $result = $this->model->update($id, $data);
        if ($result) {
            sendResponse(200, ['message' => 'Vehículo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar vehículo');
        }
    }

    private function delete($id) {
        if (empty($id)) {
            sendError(400, 'El ID del alquiler es obligatorio');
        }
    
        $result = $this->model->delete($id);
        
       
        if ($result) {

            if ($result > 0) {
                sendResponse(200, ['message' => 'Alquiler eliminado correctamente']);
            } else {
                sendError(404, 'No se encontró alquiler con ese ID');
            }
        } else {
            sendError(500, 'Error al eliminar el alquiler');
        }
    }
    
}

?>