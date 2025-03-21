<?php
require __DIR__ . '/config/conexion.php';
require __DIR__ . '/helpers/ResponseHelper.php';
require __DIR__ . '/Routes/Api-Router.php';

// Middleware CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json"); 
header("Access-Control-Allow-Origin: *"); 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// Obtener URL y método
$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Enrutamiento
$router = new ApiRouter();
$router->handle($url, $method);
?>