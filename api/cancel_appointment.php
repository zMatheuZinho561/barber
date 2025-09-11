<?php
// api/cancel_appointment.php
require_once '../config/database.php';

header('Content-Type: application/json');
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $user = getAuthenticatedUser();
    $appointmentId = $_POST['appointment_id'] ?? null;
    
    if (!$appointmentId) {
        echo json_encode([
            'success' => false,
            'message' => 'ID do agendamento é obrigatório'
        ]);
        exit;
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar se o agendamento pertence ao usuário e pode ser cancelado
    $query = "SELECT id, data_agendamento, hora_agendamento, status 
              FROM agendamentos 
              WHERE id = ? AND cliente_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$appointmentId, $user['user_id']]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        echo json_encode([
            'success' => false,
            'message' => 'Agendamento não encontrado'
        ]);
        exit;
    }
    
    // Verificar se já está cancelado
    if ($appointment['status'] === 'cancelado') {
        echo json_encode([
            'success' => false,
            'message' => 'Este agendamento já está cancelado'
        ]);
        exit;
    }
    
    // Verificar se não pode mais ser cancelado (concluído)
    if ($appointment['status'] === 'concluido') {
        echo json_encode([
            'success' => false,
            'message' => 'Não é possível cancelar um agendamento já concluído'
        ]);
        exit;
    }
    
    // Verificar se tem tempo suficiente para cancelar (2 horas de antecedência)
    $appointmentDateTime = new DateTime($appointment['data_agendamento'] . ' ' . $appointment['hora_agendamento']);
    $now = new DateTime();
    $diff = $appointmentDateTime->getTimestamp() - $now->getTimestamp();
    $hoursDiff = $diff / (60 * 60);
    
    if ($hoursDiff < 2) {
        echo json_encode([
            'success' => false,
            'message' => 'Só é possível cancelar agendamentos com pelo menos 2 horas de antecedência'
        ]);
        exit;
    }
    
    // Cancelar agendamento
    $query = "UPDATE agendamentos SET status = 'cancelado', data_cancelamento = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$appointmentId])) {
        // Log do cancelamento
        error_log("Agendamento cancelado - ID: $appointmentId, Usuario: {$user['user_id']}");
        
        echo json_encode([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao cancelar agendamento');
    }
    
} catch (Exception $e) {
    error_log("Erro ao cancelar agendamento: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>