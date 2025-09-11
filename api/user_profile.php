<?php
// api/user_profile.php
require_once '../config/database.php';

header('Content-Type: application/json');
requireAuth();

try {
    $user = getAuthenticatedUser();
    $database = new Database();
    $conn = $database->getConnection();
    
    // Buscar dados completos do usuário
    $query = "SELECT id, nome, email, telefone, data_criacao, ultimo_login FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user['user_id']]);
    $userData = $stmt->fetch();
    
    if (!$userData) {
        throw new Exception('Usuário não encontrado');
    }
    
    echo json_encode([
        'success' => true,
        'user' => $userData
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao buscar perfil: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>