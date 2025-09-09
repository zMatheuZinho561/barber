<?php
session_start();

// Definindo constante de debug
define('DEBUG', true);

// Incluindo arquivos necessários
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/security/security.php';

// Permitir requisições OPTIONS para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('Access-Control-Max-Age: 86400'); // 24 horas
    http_response_code(200);
    exit;
}

// Headers de segurança para requisições normais
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Verificar se é uma requisição AJAX


try {
    $security = new Security();
    $token = $security->generateCSRFToken();
    
    if (!$token) {
        throw new Exception('Falha ao gerar token CSRF');
    }
    
    echo json_encode([
        'success' => true,
        'token' => $token
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao gerar token CSRF: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true, 
        'message' => 'Erro interno do servidor',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}