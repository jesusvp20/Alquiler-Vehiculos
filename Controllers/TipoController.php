<?php
require_once __DIR__ . '/../helpers/ResponseHelper.php';
require_once __DIR__ . '/../model/TipoModelo.php';

class TipoController {
    private $model;

    public function __construct() {
        $this->model = new TipoModelo();
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

    private function get($id_tipo) {
        if ($id_tipo) {
            // Obtener un tipo por ID
            $tipo = $this->model->getById($id_tipo);
            if ($tipo) {
                sendResponse(200, $tipo);
            } else {
                sendError(404, 'Tipo no encontrado');
            }
        } else {
            // Listar todos los tipos
            $tipos = $this->model->getAll();
            sendResponse(200, $tipos);
        }
    }

    private function post() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos obligatorios
        if (!isset($data['nombre_tipo'])) {
            sendError(400, 'Falta el campo obligatorio: nombre_tipo');
        }

        // Crear el tipo
        $result = $this->model->create($data);
        if ($result) {
            sendResponse(201, ['message' => 'Tipo creado correctamente']);
        } else {
            sendError(500, 'Error al crear el tipo');
            exit();
        }
    }

    private function put($id_tipo) {
        if (empty($id_tipo)) {
            sendError(400, 'El ID del tipo es obligatorio');
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validar datos obligatorios
        if (!isset($data['nombre_tipo'])) {
            sendError(400, 'Falta el campo obligatorio: nombre_tipo');
        }

        // Actualizar el tipo
        $result = $this->model->update($id_tipo, $data);
        if ($result) {
            sendResponse(200, ['message' => 'Tipo actualizado correctamente']);
        } else {
            sendError(500, 'Error al actualizar el tipo');
        }
    }

    private function delete($id_tipo) {
        if (empty($id_tipo)) {
            sendError(400, 'El ID del tipo es obligatorio');
        }
        $result = $this->model->delete($id_tipo);
        if ($result) {
            sendResponse(200, ['message' => 'Tipo eliminado correctamente']);
        } else {
            sendError(500, 'Error al eliminar el tipo');
        }
    }
}