<?php
// api/user_appointments.php
require_once '../config/database.php';

header('Content-Type: application/json');
requireAuth();

try {
    $user = getAuthenticatedUser();
    $database = new Database();
    $conn = $database->getConnection();
    
    // Buscar agendamentos do usuário
    $query = "SELECT a.*, s.nome as servico_nome, s.preco, s.duracao, b.nome as barbeiro_nome 
              FROM agendamentos a
              JOIN servicos s ON a.servico_id = s.id
              JOIN barbeiros b ON a.barbeiro_id = b.id
              WHERE a.cliente_id = ?
              ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user['user_id']]);
    $appointments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'appointments' => $appointments
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao buscar agendamentos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>