<?php
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../model/ClienteModelo.php';

class ClienteController {
    private $model;

    public function __construct() {
        $this->model = new ClienteModelo();
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
            $cliente = $this->model->getById($id);
            if ($cliente) {
                sendResponse(200, $cliente);
            } else {
                sendError(404, 'Cliente no encontrado');
            }
        } else {
            $clientes = $this->model->getAll();
            sendResponse(200, $clientes);
        }
    }

    private function post() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nombre_cliente'], $data['numero_documento'])) {
            sendError(400, 'Faltan datos obligatorios: nombre_cliente y numero_documento');
        }

        $result = $this->model->create($data);
        if ($result) {
            sendResponse(201, ['message' => 'Cliente creado correctamente']);
        } else {
            sendError(500, 'Error al crear el cliente');
        }
    }

    private function put($id) {
        if (empty($id)) {
            sendError(400, 'El ID del cliente es obligatorio');
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nombre_cliente'], $data['numero_documento'])) {
            sendError(400, 'Faltan datos obligatorios: nombre_cliente y numero_documento');
        }

        $result = $this->model->update($id, $data);
        if ($result) {
            sendResponse(200, ['message' => 'Cliente actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar el cliente');
        }
    }

    private function delete($id) {
        if (empty($id)) {
            sendError(400, 'El ID del cliente es obligatorio');
        }

        // Eliminar el cliente
        $result = $this->model->delete($id);
        if ($result) {
            sendResponse(200, ['message' => 'Cliente eliminado correctamente']);
        } else {
            sendError(500, 'Error al eliminar el cliente');
        }
    }
}