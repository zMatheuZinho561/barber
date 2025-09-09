<?php
// api/get-csrf-token.php
session_start();

require_once '../security/Security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $security = new Security();
    $token = $security->generateCSRFToken();
    
    echo json_encode([
        'success' => true,
        'token' => $token
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao gerar token CSRF: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor']);
}