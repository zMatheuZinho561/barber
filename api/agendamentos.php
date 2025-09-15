<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $conn = getConnection();
    $metodo = $_SERVER['REQUEST_METHOD'];
    
    switch ($metodo) {
        case 'GET':
            $acao = $_GET['acao'] ?? 'listar';
            
            switch ($acao) {
                case 'barbeiros':
                    // Listar barbeiros ativos
                    $stmt = $conn->prepare("SELECT id, nome, especialidades, horario_inicio, horario_fim FROM barbeiros WHERE status = 'ativo' ORDER BY nome");
                    $stmt->execute();
                    $barbeiros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Se não houver barbeiros no banco, retornar dados de exemplo
                    if (empty($barbeiros)) {
                        $barbeiros = [
                            [
                                'id' => 1,
                                'nome' => 'João Silva',
                                'especialidades' => 'Cortes Clássicos e Modernos',
                                'horario_inicio' => '08:00',
                                'horario_fim' => '18:00'
                            ],
                            [
                                'id' => 2,
                                'nome' => 'Pedro Santos',
                                'especialidades' => 'Barbas e Bigodes',
                                'horario_inicio' => '09:00',
                                'horario_fim' => '17:00'
                            ],
                            [
                                'id' => 3,
                                'nome' => 'Carlos Oliveira',
                                'especialidades' => 'Cortes Premium',
                                'horario_inicio' => '10:00',
                                'horario_fim' => '19:00'
                            ]
                        ];
                    }
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $barbeiros
                    ]);
                    break;
                    
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
                    
                    // Verificar se não é domingo
                    $diaSemana = date('N', strtotime($data));
                    if ($diaSemana == 7) {
                        echo json_encode([
                            'success' => true,
                            'data' => [],
                            'message' => 'Não atendemos aos domingos'
                        ]);
                        exit();
                    }
                    
                    $horarios = obterHorariosDisponiveis($conn, $barbeiro_id, $data, $servico_id);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $horarios
                    ]);
                    break;
                    
                case 'listar':
                    // Verificar se está logado
                    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
                        throw new Exception('Usuário não logado');
                    }
                    
                    $stmt = $conn->prepare("
                        SELECT 
                            a.*,
                            b.nome as barbeiro_nome,
                            s.nome as servico_nome,
                            s.duracao as servico_duracao,
                            s.preco as servico_preco
                        FROM agendamentos a
                        LEFT JOIN barbeiros b ON a.barbeiro_id = b.id
                        LEFT JOIN servicos s ON a.servico_id = s.id
                        WHERE a.usuario_id = ?
                        ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
                    ");
                    $stmt->execute([$_SESSION['usuario_id']]);
                    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $agendamentos
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
                    // Verificar se está logado
                    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
                        throw new Exception('Usuário não logado');
                    }
                    
                    $dados = [
                        'usuario_id' => $_SESSION['usuario_id'],
                        'barbeiro_id' => $_POST['barbeiro_id'] ?? '',
                        'servico_id' => $_POST['servico_id'] ?? '',
                        'data_agendamento' => $_POST['data'] ?? '',
                        'hora_agendamento' => $_POST['horario'] ?? '',
                        'observacoes' => trim($_POST['observacoes'] ?? '')
                    ];
                    
                    // Validações
                    if (empty($dados['barbeiro_id']) || empty($dados['servico_id']) || 
                        empty($dados['data_agendamento']) || empty($dados['hora_agendamento'])) {
                        throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
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
                    
                    // Se é hoje, verificar se o horário já passou
                    if ($dados['data_agendamento'] == date('Y-m-d')) {
                        $agora = date('H:i');
                        if ($dados['hora_agendamento'] <= $agora) {
                            throw new Exception('Este horário já passou. Escolha um horário futuro.');
                        }
                    }
                    
                    // Verificar se o horário ainda está disponível
                    if (!verificarDisponibilidade($conn, $dados['barbeiro_id'], $dados['data_agendamento'], $dados['hora_agendamento'])) {
                        throw new Exception('Este horário não está mais disponível');
                    }
                    
                    // Obter preço do serviço
                    $stmt = $conn->prepare("SELECT preco FROM servicos WHERE id = ?");
                    $stmt->execute([$dados['servico_id']]);
                    $servico = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $valorTotal = $servico ? $servico['preco'] : 0;
                    
                    // Criar agendamento
                    $sql = "INSERT INTO agendamentos (usuario_id, barbeiro_id, servico_id, data_agendamento, hora_agendamento, observacoes, valor_total, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'agendado')";
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt->execute([
                        $dados['usuario_id'],
                        $dados['barbeiro_id'],
                        $dados['servico_id'],
                        $dados['data_agendamento'],
                        $dados['hora_agendamento'],
                        $dados['observacoes'],
                        $valorTotal
                    ])) {
                        $agendamentoId = $conn->lastInsertId();
                        
                        // Buscar dados completos do agendamento criado
                        $stmt = $conn->prepare("
                            SELECT 
                                a.*,
                                b.nome as barbeiro_nome,
                                s.nome as servico_nome,
                                s.duracao as servico_duracao,
                                s.preco as servico_preco
                            FROM agendamentos a
                            LEFT JOIN barbeiros b ON a.barbeiro_id = b.id
                            LEFT JOIN servicos s ON a.servico_id = s.id
                            WHERE a.id = ?
                        ");
                        $stmt->execute([$agendamentoId]);
                        $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        echo json_encode([
                            'success' => true,
                            'message' => 'Agendamento realizado com sucesso!',
                            'data' => $agendamento
                        ]);
                    } else {
                        throw new Exception('Erro ao criar agendamento');
                    }
                    break;
                    
                case 'cancelar':
                    // Verificar se está logado
                    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
                        throw new Exception('Usuário não logado');
                    }
                    
                    $id = $_POST['id'] ?? '';
                    
                    if (empty($id)) {
                        throw new Exception('ID do agendamento é obrigatório');
                    }
                    
                    // Verificar se o agendamento pertence ao usuário
                    $stmt = $conn->prepare("SELECT * FROM agendamentos WHERE id = ? AND usuario_id = ?");
                    $stmt->execute([$id, $_SESSION['usuario_id']]);
                    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$agendamento) {
                        throw new Exception('Agendamento não encontrado');
                    }
                    
                    if ($agendamento['status'] === 'cancelado') {
                        throw new Exception('Agendamento já está cancelado');
                    }
                    
                    // Verificar se pode cancelar (pelo menos 2 horas antes)
                    $dataHoraAgendamento = strtotime($agendamento['data_agendamento'] . ' ' . $agendamento['hora_agendamento']);
                    $agora = time();
                    $duasHoras = 2 * 60 * 60; // 2 horas em segundos
                    
                    if ($dataHoraAgendamento - $agora < $duasHoras) {
                        throw new Exception('Cancelamento deve ser feito com pelo menos 2 horas de antecedência');
                    }
                    
                    // Cancelar o agendamento
                    $stmt = $conn->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ?");
                    
                    if ($stmt->execute([$id])) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Agendamento cancelado com sucesso!'
                        ]);
                    } else {
                        throw new Exception('Erro ao cancelar agendamento');
                    }
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

// Função para verificar disponibilidade do horário
function verificarDisponibilidade($conn, $barbeiro_id, $data, $hora) {
    try {
        $stmt = $conn->prepare("
            SELECT id FROM agendamentos 
            WHERE barbeiro_id = ? 
            AND data_agendamento = ? 
            AND hora_agendamento = ? 
            AND status NOT IN ('cancelado')
        ");
        $stmt->execute([$barbeiro_id, $data, $hora]);
        
        return $stmt->rowCount() === 0;
    } catch (Exception $e) {
        return false;
    }
}

// Função para obter horários disponíveis
function obterHorariosDisponiveis($conn, $barbeiro_id, $data, $servico_id = null) {
    try {
        // Primeiro, tentar obter do banco de dados
        $stmt = $conn->prepare("SELECT horario_inicio, horario_fim FROM barbeiros WHERE id = ? AND status = 'ativo'");
        $stmt->execute([$barbeiro_id]);
        $barbeiro = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se não encontrar no banco, usar horários padrão
        if (!$barbeiro) {
            $horariosDefault = [
                '1' => ['horario_inicio' => '08:00', 'horario_fim' => '18:00'],
                '2' => ['horario_inicio' => '09:00', 'horario_fim' => '17:00'],
                '3' => ['horario_inicio' => '10:00', 'horario_fim' => '19:00']
            ];
            $barbeiro = $horariosDefault[$barbeiro_id] ?? ['horario_inicio' => '08:00', 'horario_fim' => '18:00'];
        }
        
        // Obter duração do serviço
        $duracaoServico = 30; // padrão
        if ($servico_id) {
            $stmt = $conn->prepare("SELECT duracao FROM servicos WHERE id = ?");
            $stmt->execute([$servico_id]);
            $servico = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($servico) {
                $duracaoServico = $servico['duracao'];
            }
        }
        
        // Gerar horários possíveis
        $horariosDisponiveis = [];
        $horaInicio = strtotime($barbeiro['horario_inicio']);
        $horaFim = strtotime($barbeiro['horario_fim']);
        $intervalo = 30 * 60; // 30 minutos
        
        // Se é hoje, ajustar hora de início para não mostrar horários passados
        $agora = time();
        $dataHoje = date('Y-m-d');
        
        if ($data == $dataHoje) {
            $horaAtual = strtotime(date('H:i'));
            // Adicionar um buffer de 30 minutos
            $horaAtual += (30 * 60);
            
            // Arredondar para o próximo slot de 30 minutos
            $minutos = date('i', $horaAtual);
            if ($minutos > 0 && $minutos <= 30) {
                $horaAtual = strtotime(date('H:30', $horaAtual));
            } else if ($minutos > 30) {
                $horaAtual = strtotime(date('H:00', $horaAtual + 3600));
            }
            
            if ($horaAtual > $horaInicio) {
                $horaInicio = $horaAtual;
            }
        }
        
        // Obter horários já agendados
        $stmt = $conn->prepare("
            SELECT hora_agendamento, COALESCE(s.duracao, 30) as duracao
            FROM agendamentos a
            LEFT JOIN servicos s ON a.servico_id = s.id
            WHERE a.barbeiro_id = ? 
            AND a.data_agendamento = ? 
            AND a.status NOT IN ('cancelado')
            ORDER BY a.hora_agendamento
        ");
        $stmt->execute([$barbeiro_id, $data]);
        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Criar array de horários ocupados
        $horariosOcupados = [];
        foreach ($agendamentos as $agendamento) {
            $inicioAgendamento = strtotime($agendamento['hora_agendamento']);
            $fimAgendamento = $inicioAgendamento + ($agendamento['duracao'] * 60);
            
            // Marcar todos os slots ocupados por este agendamento
            for ($time = $inicioAgendamento; $time < $fimAgendamento; $time += $intervalo) {
                $horariosOcupados[] = date('H:i', $time);
            }
        }
        
        // Verificar disponibilidade para cada horário
        for ($time = $horaInicio; $time < $horaFim; $time += $intervalo) {
            $horario = date('H:i', $time);
            $fimDesejado = $time + ($duracaoServico * 60);
            
            // Verificar se o serviço completo cabe no horário de trabalho
            if ($fimDesejado > $horaFim) {
                continue;
            }
            
            // Verificar se algum slot necessário está ocupado
            $disponivel = true;
            for ($checkTime = $time; $checkTime < $fimDesejado; $checkTime += $intervalo) {
                if (in_array(date('H:i', $checkTime), $horariosOcupados)) {
                    $disponivel = false;
                    break;
                }
            }
            
            if ($disponivel) {
                $horariosDisponiveis[] = $horario;
            }
        }
        
        return $horariosDisponiveis;
        
    } catch (Exception $e) {
        // Em caso de erro, retornar horários padrão (excluindo os que já passaram)
        $horariosDefault = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'];
        
        // Se é hoje, filtrar horários que já passaram
        if ($data == date('Y-m-d')) {
            $agora = date('H:i');
            $horariosDefault = array_filter($horariosDefault, function($horario) use ($agora) {
                return $horario > $agora;
            });
        }
        
        return array_values($horariosDefault);
    }
}
?>