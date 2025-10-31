<?php
require_once __DIR__ . '/../includes/db_include.php';
require_once __DIR__ . '/../Controllers/AgendamentoController.php';

// Habilita CORS para desenvolvimento
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$controller = new AgendamentoController();
$response = null;

// Extrair ID da URL se presente
$pathInfo = $_SERVER['PATH_INFO'] ?? '';
$id = null;
if (preg_match('/\/(\d+)/', $pathInfo, $matches)) {
    $id = $matches[1];
}

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($id) {
                $response = $controller->visualizar($id);
            } else {
                $response = $controller->index();
            }
            break;
            
        case 'POST':
            $response = $controller->criar();
            break;
            
        case 'PUT':
            if (!$id) {
                throw new Exception('ID não fornecido');
            }
            $response = $controller->atualizar($id);
            break;
            
        case 'DELETE':
            if (!$id) {
                throw new Exception('ID não fornecido');
            }
            $response = $controller->excluir($id);
            break;
            
        default:
            throw new Exception('Método não suportado');
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
    http_response_code(500);
}

echo json_encode($response);