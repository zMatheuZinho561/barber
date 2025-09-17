<?php
// Limpar qualquer output anterior e definir headers
ob_clean();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Função para retornar JSON e parar execução
function jsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Função para retornar erro
function jsonError($message, $httpCode = 400) {
    jsonResponse(['error' => $message], $httpCode);
}

try {
    // Só inicia sessão se não estiver ativa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Incluir arquivos necessários - ajustar caminho conforme sua estrutura
    $authPath = file_exists('../includes/auth.php') ? '../includes/auth.php' : '../include/auth.php';
    if (!file_exists($authPath)) {
        jsonError('Arquivo de autenticação não encontrado');
    }
    
    require_once $authPath;

    $auth = new Auth();
    
    // Verificar se está logado
    if (!$auth->isLoggedIn()) {
        jsonError('Usuário não autenticado', 401);
    }

    $conn = getDBConnection();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'get_servicos':
            $stmt = $conn->query("SELECT * FROM servicos WHERE ativo = 1 ORDER BY nome");
            $servicos = $stmt->fetchAll();
            jsonResponse($servicos);
            break;
            
        case 'get_barbeiros':
            $stmt = $conn->query("SELECT id, nome, especialidades FROM barbeiros WHERE ativo = 1 ORDER BY nome");
            $barbeiros = $stmt->fetchAll();
            jsonResponse($barbeiros);
            break;
            
        case 'get_horarios_disponiveis':
            $data = $_GET['data'] ?? '';
            $servico_id = $_GET['servico_id'] ?? '';
            $barbeiro_id = $_GET['barbeiro_id'] ?? '';
            
            if (!$data || !$servico_id || !$barbeiro_id) {
                jsonError('Data, serviço e barbeiro são obrigatórios');
            }
            
            // Validar formato da data
            $dateObj = DateTime::createFromFormat('Y-m-d', $data);
            if (!$dateObj) {
                jsonError('Formato de data inválido');
            }
            
            // Verificar se não é data passada
            if ($dateObj < new DateTime('today')) {
                jsonError('Não é possível agendar para datas passadas');
            }
            
            // Buscar duração do serviço
            $stmt = $conn->prepare("SELECT duracao FROM servicos WHERE id = ? AND ativo = 1");
            $stmt->execute([$servico_id]);
            $servico = $stmt->fetch();
            
            if (!$servico) {
                jsonError('Serviço não encontrado');
            }
            
            // Verificar se barbeiro existe e está ativo
            $stmt = $conn->prepare("SELECT nome FROM barbeiros WHERE id = ? AND ativo = 1");
            $stmt->execute([$barbeiro_id]);
            $barbeiro = $stmt->fetch();
            
            if (!$barbeiro) {
                jsonError('Barbeiro não encontrado ou inativo');
            }
            
            $duracao_servico = $servico['duracao'];
            
            // Buscar horários de trabalho do barbeiro para o dia da semana
            $dayOfWeek = $dateObj->format('w'); // 0 = domingo, 1 = segunda, etc.
            $diasSemana = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            $diaNome = $diasSemana[$dayOfWeek];
            
            $stmt = $conn->prepare("
                SELECT hora_inicio, hora_fim 
                FROM barbeiro_horarios 
                WHERE barbeiro_id = ? AND dia_semana = ? AND ativo = 1
            ");
            $stmt->execute([$barbeiro_id, $diaNome]);
            $horarioTrabalho = $stmt->fetch();
            
            if (!$horarioTrabalho) {
                jsonResponse([]); // Barbeiro não trabalha neste dia
            }
            
            // Converter horários para timestamp
            $inicio_trabalho = strtotime($horarioTrabalho['hora_inicio']);
            $fim_trabalho = strtotime($horarioTrabalho['hora_fim']);
            
            // Gerar todos os horários possíveis (intervalos de 30 minutos)
            $horarios_possiveis = [];
            for ($timestamp = $inicio_trabalho; $timestamp < $fim_trabalho; $timestamp += 1800) { // 1800 segundos = 30 minutos
                $horario_str = date('H:i:s', $timestamp);
                
                // Calcular horário de fim do serviço
                $fim_servico = date('H:i:s', $timestamp + ($duracao_servico * 60));
                
                // Verificar se o serviço termina dentro do horário de funcionamento
                if (strtotime($fim_servico) <= $fim_trabalho) {
                    $horarios_possiveis[] = $horario_str;
                }
            }
            
            // Buscar horários já agendados para este barbeiro nesta data
            $stmt = $conn->prepare("
                SELECT a.hora_agendamento, s.duracao 
                FROM agendamentos a 
                JOIN servicos s ON a.servico_id = s.id 
                WHERE a.data_agendamento = ? 
                AND a.barbeiro_id = ?
                AND a.status IN ('agendado', 'confirmado')
            ");
            $stmt->execute([$data, $barbeiro_id]);
            $agendamentos_existentes = $stmt->fetchAll();
            
            // Filtrar horários disponíveis
            $horarios_disponiveis = [];
            foreach ($horarios_possiveis as $horario) {
                $disponivel = true;
                
                foreach ($agendamentos_existentes as $agendamento) {
                    $inicio_existente = strtotime($agendamento['hora_agendamento']);
                    $fim_existente = $inicio_existente + ($agendamento['duracao'] * 60);
                    
                    $inicio_novo = strtotime($horario);
                    $fim_novo = $inicio_novo + ($duracao_servico * 60);
                    
                    // Verificar conflito de horários
                    if ($inicio_novo < $fim_existente && $fim_novo > $inicio_existente) {
                        $disponivel = false;
                        break;
                    }
                }
                
                if ($disponivel) {
                    $horarios_disponiveis[] = [
                        'horario' => $horario,
                        'horario_formatado' => date('H:i', strtotime($horario))
                    ];
                }
            }
            
            jsonResponse($horarios_disponiveis);
            break;
            
        case 'criar_agendamento':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonError('Método não permitido', 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonError('JSON inválido: ' . json_last_error_msg());
            }
            
            $servico_id = $input['servico_id'] ?? '';
            $barbeiro_id = $input['barbeiro_id'] ?? '';
            $data_agendamento = $input['data_agendamento'] ?? '';
            $hora_agendamento = $input['hora_agendamento'] ?? '';
            $observacoes = $input['observacoes'] ?? '';
            
            if (!$servico_id || !$barbeiro_id || !$data_agendamento || !$hora_agendamento) {
                jsonError('Todos os campos obrigatórios devem ser preenchidos');
            }
            
            // Verificar se não é admin tentando agendar
            if ($auth->isAdmin()) {
                jsonError('Administradores não podem fazer agendamentos');
            }
            
            // Verificar se a data não é no passado
            if (strtotime($data_agendamento) < strtotime(date('Y-m-d'))) {
                jsonError('Não é possível agendar para datas passadas');
            }
            
            // Verificar se o serviço existe e está ativo
            $stmt = $conn->prepare("SELECT * FROM servicos WHERE id = ? AND ativo = 1");
            $stmt->execute([$servico_id]);
            $servico = $stmt->fetch();
            
            if (!$servico) {
                jsonError('Serviço não encontrado ou inativo');
            }
            
            // Verificar se o barbeiro existe e está ativo
            $stmt = $conn->prepare("SELECT * FROM barbeiros WHERE id = ? AND ativo = 1");
            $stmt->execute([$barbeiro_id]);
            $barbeiro = $stmt->fetch();
            
            if (!$barbeiro) {
                jsonError('Barbeiro não encontrado ou inativo');
            }
            
            // Verificar se o horário ainda está disponível (dupla verificação)
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count 
                FROM agendamentos a 
                JOIN servicos s ON a.servico_id = s.id 
                WHERE a.data_agendamento = ? 
                AND a.barbeiro_id = ?
                AND a.status IN ('agendado', 'confirmado')
                AND (
                    (? >= a.hora_agendamento AND ? < ADDTIME(a.hora_agendamento, SEC_TO_TIME(s.duracao * 60))) OR
                    (a.hora_agendamento >= ? AND a.hora_agendamento < ADDTIME(?, SEC_TO_TIME(? * 60)))
                )
            ");
            $stmt->execute([
                $data_agendamento,
                $barbeiro_id,
                $hora_agendamento,
                $hora_agendamento,
                $hora_agendamento,
                $hora_agendamento,
                $servico['duracao']
            ]);
            
            if ($stmt->fetch()['count'] > 0) {
                jsonError('Horário não está mais disponível para este barbeiro');
            }
            
            // Criar agendamento
            $stmt = $conn->prepare("
                INSERT INTO agendamentos (usuario_id, servico_id, barbeiro_id, data_agendamento, hora_agendamento, observacoes, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'agendado')
            ");
            $stmt->execute([
                $_SESSION['usuario_id'],
                $servico_id,
                $barbeiro_id,
                $data_agendamento,
                $hora_agendamento,
                $observacoes
            ]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Agendamento criado com sucesso!',
                'agendamento_id' => $conn->lastInsertId(),
                'barbeiro_nome' => $barbeiro['nome']
            ]);
            break;
            
        // Endpoints para admin
        case 'get_agenda_barbeiro':
            if (!$auth->isAdmin()) {
                jsonError('Acesso negado', 403);
            }
            
            $barbeiro_id = $_GET['barbeiro_id'] ?? '';
            $data = $_GET['data'] ?? date('Y-m-d');
            
            if (!$barbeiro_id) {
                jsonError('ID do barbeiro é obrigatório');
            }
            
            $stmt = $conn->prepare("
                SELECT * FROM vw_agenda_barbeiros 
                WHERE barbeiro_id = ? AND data_agendamento = ?
                ORDER BY hora_agendamento
            ");
            $stmt->execute([$barbeiro_id, $data]);
            $agenda = $stmt->fetchAll();
            
            jsonResponse($agenda);
            break;
            
        case 'get_resumo_dia':
            if (!$auth->isAdmin()) {
                jsonError('Acesso negado', 403);
            }
            
            $data = $_GET['data'] ?? date('Y-m-d');
            
            // Resumo por barbeiro
            $stmt = $conn->prepare("
                SELECT 
                    b.id,
                    b.nome,
                    COUNT(a.id) as total_agendamentos,
                    SUM(CASE WHEN a.status = 'agendado' THEN 1 ELSE 0 END) as agendados,
                    SUM(CASE WHEN a.status = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
                    SUM(CASE WHEN a.status = 'realizado' THEN 1 ELSE 0 END) as realizados,
                    SUM(CASE WHEN a.status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(s.preco) as receita_potencial
                FROM barbeiros b
                LEFT JOIN agendamentos a ON b.id = a.barbeiro_id AND a.data_agendamento = ?
                LEFT JOIN servicos s ON a.servico_id = s.id
                WHERE b.ativo = 1
                GROUP BY b.id, b.nome
                ORDER BY b.nome
            ");
            $stmt->execute([$data]);
            $resumo = $stmt->fetchAll();
            
            jsonResponse($resumo);
            break;
            
        case 'update_status':
            if (!$auth->isAdmin()) {
                jsonError('Acesso negado', 403);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonError('Método não permitido', 405);
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                jsonError('JSON inválido: ' . json_last_error_msg());
            }
            
            $agendamento_id = $input['agendamento_id'] ?? '';
            $status = $input['status'] ?? '';
            
            if (!$agendamento_id || !$status) {
                jsonError('ID do agendamento e status são obrigatórios');
            }
            
            // Validar status
            $statusValidos = ['agendado', 'confirmado', 'realizado', 'cancelado'];
            if (!in_array($status, $statusValidos)) {
                jsonError('Status inválido');
            }
            
            // Verificar se agendamento existe
            $stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id = ?");
            $stmt->execute([$agendamento_id]);
            $agendamento = $stmt->fetch();
            
            if (!$agendamento) {
                jsonError('Agendamento não encontrado');
            }
            
            // Atualizar status
            $stmt = $conn->prepare("UPDATE agendamentos SET status = ?, data_atualizacao = NOW() WHERE id = ?");
            $stmt->execute([$status, $agendamento_id]);
            
            jsonResponse([
                'success' => true,
                'message' => 'Status atualizado com sucesso!',
                'novo_status' => $status
            ]);
            break;
            
        default:
            jsonError('Ação não encontrada', 404);
    }
    
} catch (PDOException $e) {
    jsonError('Erro no banco de dados: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    jsonError('Erro interno: ' . $e->getMessage(), 500);
}
?>