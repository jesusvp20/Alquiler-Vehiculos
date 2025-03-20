<?php
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../model/VehiculoModelo.php';

class VehiculoController {
    private $model;

    public function __construct() {
        $this->model = new VehiculoModel();
    }

    public function handleRequest($method, $placa = null) {
        switch ($method) {
            case 'GET':
                $this->get($placa);
                break;
            case 'POST':
                $this->post();
                break;
            case 'PUT':
                if (!$placa) {
                    sendError(400, 'Se requiere la placa del vehículo para actualizar.');
                    return;
                }
                $this->put($placa);
                break;
            case 'DELETE':
                if (!$placa) {
                    sendError(400, 'Se requiere la placa del vehículo para eliminar.');
                    return;
                }
                $this->delete($placa);
                break;
            default:
                sendError(405, 'Método no permitido');
        }
    }

    public function get($placa) {
        if ($placa) {
            $data = $this->model->getByPlaca($placa);
            if ($data) {
                sendResponse(200, $data);
            } else {
                sendError(404, 'Vehículo no encontrado');
            }
        } else {
            $vehiculos = $this->model->getAll();
            sendResponse(200, $vehiculos);
        }
    }

    public function getByTipo($id_tipo) {
        $data = $this->model->getByTipo($id_tipo);
        if ($data) {
            sendResponse(200, $data);
        } else {
            sendError(404, 'No se encontraron vehículos para este tipo');
        }
    }
    
    private function post() {
        header('Content-Type: application/json; charset=utf-8');
    
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (!isset($data['modelo_vehiculo'], $data['id_tipo'])) {
            sendError(400, 'Faltan datos obligatorios (modelo_vehiculo, id_tipo)');
            return;
        }
    
        $result = $this->model->create($data);
    
        if (isset($result['error'])) {
            sendError(500, $result['error']);
        } else {
           
            sendResponse(201, ['message' => 'Vehículo creado correctamente']);
        }
    }

    private function put($placa) {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['modelo_vehiculo'], $data['id_tipo'])) {
            sendError(400, 'Faltan datos obligatorios (modelo_vehiculo, id_tipo)');
            return;
        }

        $result = $this->model->update($placa, $data);
        if ($result) {
            sendResponse(200, ['message' => 'Vehículo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar vehículo');
        }
    }

    private function delete($placa) {
        $result = $this->model->delete($placa);
        if (isset($result['success'])) {
            sendResponse(200, ['message' => $result['success']]);
        } else {
            sendError(500, $result['error']);
        }
    }
}
?>
