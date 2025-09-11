<?php
session_start();
header('Content-Type: application/json');

// Verificar se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
    exit();
}

require_once '../models/Agendamento.php';
require_once '../models/Barbeiro.php';

try {
    $agendamento = new Agendamento();
    $barbeiro = new Barbeiro();
    $metodo = $_SERVER['REQUEST_METHOD'];
    
    switch ($metodo) {
        case 'GET':
            $acao = $_GET['acao'] ?? 'listar';
            
            switch ($acao) {
                case 'horarios':
                    $barbeiro_id = $_GET['barbeiro_id'] ?? '';
                    $data = $_GET['data'] ?? '';
                    $servico_id = $_GET['servico_id'] ?? null;
                    
                    if (empty($barbeiro_id) || empty($data)) {
                        throw new Exception('Barbeiro e data são obrigatórios');
                    }
                    
                    // Verificar se a data não é no passado
                    if (strtotime($data) < strtotime(date('Y-m-d'))) {
                        throw new Exception('Não é possível agendar para datas passadas');
                    }
                    
                    // Verificar se o barbeiro trabalha neste dia
                    if (!$barbeiro->verificarDisponibilidade($barbeiro_id, $data)) {
                        echo json_encode([
                            'success' => true,
                            'data' => [],
                            'message' => 'Barbeiro não trabalha neste dia'
                        ]);
                        exit();
                    }
                    
                    $horarios = $agendamento->obterHorariosDisponiveis($barbeiro_id, $data, $servico_id);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $horarios
                    ]);
                    break;
                    
                case 'listar':
                    $agendamentos = $agendamento->listarPorUsuario($_SESSION['usuario_id']);
                    echo json_encode([
                        'success' => true,
                        'data' => $agendamentos
                    ]);
                    break;
                    
                case 'barbeiros':
                    $barbeiros = $barbeiro->listarAtivos();
                    echo json_encode([
                        'success' => true,
                        'data' => $barbeiros
                    ]);
                    break;
                    
                default:
                    throw new Exception('Ação não reconhecida');
            }
            break;
            
        case 'POST':
            $acao = $_POST['acao'] ?? 'criar';
            
            switch ($acao) {
                case 'criar':
                    $dados = [
                        'usuario_id' => $_SESSION['usuario_id'],
                        'barbeiro_id' => $_POST['barbeiro_id'] ?? '',
                        'servico_id' => $_POST['servico_id'] ?? '',
                        'data_agendamento' => $_POST['data'] ?? '',
                        'hora_agendamento' => $_POST['horario'] ?? '',
                        'observacoes' => $_POST['observacoes'] ?? ''
                    ];
                    
                    // Validações
                    if (empty($dados['barbeiro_id']) || empty($dados['servico_id']) || 
                        empty($dados['data_agendamento']) || empty($dados['hora_agendamento'])) {
                        throw new Exception('Todos os campos são obrigatórios');
                    }
                    
                    // Verificar se a data não é no passado
                    if (strtotime($dados['data_agendamento']) < strtotime(date('Y-m-d'))) {
                        throw new Exception('Não é possível agendar para datas passadas');
                    }
                    
                    // Verificar se não é domingo
                    $diaSemana = date('N', strtotime($dados['data_agendamento']));
                    if ($diaSemana == 7) {
                        throw new Exception('Não atendemos aos domingos');
                    }
                    
                    $resultado = $agendamento->criar($dados);
                    echo json_encode($resultado);
                    break;
                    
                case 'cancelar':
                    $id = $_POST['id'] ?? '';
                    
                    if (empty($id)) {
                        throw new Exception('ID do agendamento é obrigatório');
                    }
                    
                    $resultado = $agendamento->cancelar($id, $_SESSION['usuario_id']);
                    echo json_encode($resultado);
                    break;
                    
                default:
                    throw new Exception('Ação não reconhecida');
            }
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>