<?php
// api/check_auth.php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $user = getAuthenticatedUser();
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'authenticated' => true,
            'user' => [
                'id' => $user['user_id'],
                'nome' => $user['nome'],
                'email' => $user['email'],
                'tipo' => $user['tipo']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'authenticated' => false,
            'user' => null
        ]);
    }
    
} catch (Exception $e) {
    error_log("Erro em check_auth: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>