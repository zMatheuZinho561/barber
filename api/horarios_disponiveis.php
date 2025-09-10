<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$barbeiro_id = $_POST['barbeiro_id'] ?? null;
$data = $_POST['data'] ?? null;

if (!$barbeiro_id || !$data) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros obrigatórios']);
    exit;
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar se a data é válida e não é no passado
    $data_obj = DateTime::createFromFormat('Y-m-d', $data);
    $hoje = new DateTime();
    
    if (!$data_obj || $data_obj < $hoje) {
        echo json_encode(['success' => false, 'message' => 'Data inválida']);
        exit;
    }
    
    // Obter dia da semana (1 = segunda, 7 = domingo)
    $dia_semana = $data_obj->format('N');
    
    // Buscar horários de trabalho do barbeiro para este dia da semana
    $query = "SELECT hora_inicio, hora_fim FROM horarios_barbeiros 
              WHERE barbeiro_id = ? AND dia_semana = ? AND ativo = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$barbeiro_id, $dia_semana]);
    $horario_trabalho = $stmt->fetch();
    
    if (!$horario_trabalho) {
        echo json_encode(['success' => true, 'horarios' => []]);
        exit;
    }
    
    // Buscar agendamentos já marcados para este barbeiro nesta data
    $query = "SELECT hora_agendamento FROM agendamentos 
              WHERE barbeiro_id = ? AND data_agendamento = ? 
              AND status IN ('agendado', 'confirmado')";
    $stmt = $conn->prepare($query);
    $stmt->execute([$barbeiro_id, $data]);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Gerar horários disponíveis (de 30 em 30 minutos)
    $horarios_disponiveis = [];
    $inicio = new DateTime($horario_trabalho['hora_inicio']);
    $fim = new DateTime($horario_trabalho['hora_fim']);
    $agora = new DateTime();
    
    while ($inicio < $fim) {
        $horario_str = $inicio->format('H:i');
        
        // Se é hoje, só mostrar horários futuros (com 1 hora de antecedência)
        $data_hora_agendamento = new DateTime($data . ' ' . $horario_str);
        $limite_hoje = clone $agora;
        $limite_hoje->add(new DateInterval('PT1H'));
        
        if ($data_obj->format('Y-m-d') === $hoje->format('Y-m-d')) {
            if ($data_hora_agendamento <= $limite_hoje) {
                $inicio->add(new DateInterval('PT30M'));
                continue;
            }
        }
        
        // Verificar se este horário já está ocupado
        if (!in_array($horario_str . ':00', $agendamentos) && !in_array($horario_str, $agendamentos)) {
            $horarios_disponiveis[] = ['hora' => $horario_str];
        }
        
        $inicio->add(new DateInterval('PT30M'));
    }
    
    echo json_encode([
        'success' => true, 
        'horarios' => $horarios_disponiveis
    ]);
    
} catch (Exception $e) {
    error_log("Erro em horarios_disponiveis.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno']);
}
?>