<?php
class ApiRouter {
    public function handle($url, $method) {
        $segments = explode('/', trim($url, '/'));
        $resource = $segments[0] ?? '';

        if ($resource === 'vehiculos' && isset($segments[1]) && $segments[1] === 'tipo') {
            require_once __DIR__ . '/../controllers/VehiculoController.php';
            $controller = new VehiculoController();
            $id_tipo = $segments[2] ?? null;
            if ($method === 'GET') {
                $controller->getByTipo($id_tipo);
            } else {
                sendError(405, 'Método no permitido para esta ruta');
            }
            return;
        }

        switch ($resource) {
            case 'vehiculos':
                require_once __DIR__ . '/../controllers/VehiculoController.php';
                $controller = new VehiculoController();
                break;
            case 'clientes':
                require_once __DIR__ . '/../controllers/ClienteController.php';
                $controller = new ClienteController();
                break;
            case 'alquiler':
                require_once __DIR__ . '/../controllers/AlquilerController.php';
                $controller = new AlquilerController();
                break;
            case 'tipos':
                require_once __DIR__ . '/../controllers/TipoController.php';
                $controller = new TipoController();
                break;
            default:
                sendError(404, 'Endpoint no válido');
                exit();
        }

        $id = $segments[1] ?? null;
        $controller->handleRequest($method, $id);
    }
}


?>
