<?php
// api/admin_dashboard.php
require_once '../config/database.php';

header('Content-Type: application/json');
requireAdmin();

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $data = [];
    
    // Estatísticas básicas
    $stats = [];
    
    // Total de agendamentos
    $query = "SELECT COUNT(*) as total FROM agendamentos";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['total_agendamentos'] = $stmt->fetchColumn();
    
    // Agendamentos hoje
    $query = "SELECT COUNT(*) as total FROM agendamentos WHERE DATE(data_agendamento) = CURDATE()";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['agendamentos_hoje'] = $stmt->fetchColumn();
    
    // Total de clientes
    $query = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'cliente' AND ativo = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['total_clientes'] = $stmt->fetchColumn();
    
    // Receita do mês
    $query = "SELECT COALESCE(SUM(valor), 0) as receita FROM agendamentos 
              WHERE MONTH(data_agendamento) = MONTH(CURDATE()) 
              AND YEAR(data_agendamento) = YEAR(CURDATE())
              AND status = 'concluido'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stats['receita_mes'] = $stmt->fetchColumn();
    
    $data['stats'] = $stats;
    
    // Dados mensais para gráfico (últimos 12 meses)
    $query = "SELECT 
                DATE_FORMAT(data_agendamento, '%Y-%m') as month,
                COUNT(*) as count
              FROM agendamentos 
              WHERE data_agendamento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(data_agendamento, '%Y-%m')
              ORDER BY month";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $monthlyData = $stmt->fetchAll();
    
    // Formatear dados mensais
    $data['monthly_data'] = array_map(function($row) {
        return [
            'month' => date('M/y', strtotime($row['month'] . '-01')),
            'count' => (int)$row['count']
        ];
    }, $monthlyData);
    
    // Dados de status para gráfico
    $query = "SELECT status, COUNT(*) as count FROM agendamentos 
              WHERE data_agendamento >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY status";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data['status_data'] = $stmt->fetchAll();
    
    // Agendamentos recentes
    $query = "SELECT a.*, u.nome as cliente_nome, s.nome as servico_nome, b.nome as barbeiro_nome
              FROM agendamentos a
              JOIN usuarios u ON a.cliente_id = u.id
              JOIN servicos s ON a.servico_id = s.id
              JOIN barbeiros b ON a.barbeiro_id = b.id
              ORDER BY a.data_criacao DESC
              LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $data['recent_appointments'] = $stmt->fetchAll();
    
    // Dados do usuário
    $user = getAuthenticatedUser();
    $data['user'] = [
        'nome' => $user['nome'],
        'email' => $user['email'],
        'tipo' => $user['tipo']
    ];
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    error_log("Erro no dashboard admin: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>