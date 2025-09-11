<?php
// models/Agendamento.php
require_once '../config/database.php';

class Agendamento {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function criar($dados) {
        try {
            // Verificar se o horário ainda está disponível
            if (!$this->verificarDisponibilidade($dados['barbeiro_id'], $dados['data_agendamento'], $dados['hora_agendamento'])) {
                return ['success' => false, 'message' => 'Este horário não está mais disponível!'];
            }
            
            // Obter preço do serviço
            $stmt = $this->conn->prepare("SELECT preco FROM servicos WHERE id = ?");
            $stmt->execute([$dados['servico_id']]);
            $servico = $stmt->fetch();
            
            if (!$servico) {
                return ['success' => false, 'message' => 'Serviço não encontrado!'];
            }
            
            $sql = "INSERT INTO agendamentos (usuario_id, barbeiro_id, servico_id, data_agendamento, hora_agendamento, observacoes, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([
                $dados['usuario_id'],
                $dados['barbeiro_id'],
                $dados['servico_id'],
                $dados['data_agendamento'],
                $dados['hora_agendamento'],
                $dados['observacoes'] ?? null,
                $servico['preco']
            ])) {
                $agendamentoId = $this->conn->lastInsertId();
                
                // Buscar dados completos do agendamento criado
                $agendamento = $this->obterPorId($agendamentoId);
                
                return [
                    'success' => true, 
                    'message' => 'Agendamento realizado com sucesso!',
                    'data' => $agendamento
                ];
            }
            
            return ['success' => false, 'message' => 'Erro ao criar agendamento!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function verificarDisponibilidade($barbeiro_id, $data, $hora) {
        try {
            $stmt = $this->conn->prepare("
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
    
    public function obterHorariosDisponiveis($barbeiro_id, $data, $servico_id = null) {
        try {
            // Obter informações do barbeiro
            $stmt = $this->conn->prepare("SELECT horario_inicio, horario_fim FROM barbeiros WHERE id = ? AND status = 'ativo'");
            $stmt->execute([$barbeiro_id]);
            $barbeiro = $stmt->fetch();
            
            if (!$barbeiro) {
                return [];
            }
            
            // Obter duração do serviço (se fornecido)
            $duracaoServico = 30; // padrão
            if ($servico_id) {
                $stmt = $this->conn->prepare("SELECT duracao FROM servicos WHERE id = ?");
                $stmt->execute([$servico_id]);
                $servico = $stmt->fetch();
                if ($servico) {
                    $duracaoServico = $servico['duracao'];
                }
            }
            
            // Gerar horários possíveis
            $horariosDisponiveis = [];
            $horaInicio = strtotime($barbeiro['horario_inicio']);
            $horaFim = strtotime($barbeiro['horario_fim']);
            $intervalo = 30 * 60; // 30 minutos em segundos
            
            // Obter horários já agendados
            $stmt = $this->conn->prepare("
                SELECT hora_agendamento, s.duracao
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                WHERE a.barbeiro_id = ? 
                AND a.data_agendamento = ? 
                AND a.status NOT IN ('cancelado')
                ORDER BY a.hora_agendamento
            ");
            $stmt->execute([$barbeiro_id, $data]);
            $agendamentos = $stmt->fetchAll();
            
            // Criar array de horários ocupados
            $horariosOcupados = [];
            foreach ($agendamentos as $agendamento) {
                $inicioAgendamento = strtotime($agendamento['hora_agendamento']);
                $fimAgendamento = $inicioAgendamento + ($agendamento['duracao'] * 60);
                
                // Marcar todos os slots ocupados
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
            return [];
        }
    }
    
    public function obterPorId($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    a.*,
                    u.nome as cliente_nome,
                    u.telefone as cliente_telefone,
                    b.nome as barbeiro_nome,
                    s.nome as servico_nome,
                    s.duracao as servico_duracao
                FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN barbeiros b ON a.barbeiro_id = b.id
                JOIN servicos s ON a.servico_id = s.id
                WHERE a.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function listarPorUsuario($usuario_id, $status = null) {
        try {
            $sql = "
                SELECT 
                    a.*,
                    b.nome as barbeiro_nome,
                    s.nome as servico_nome,
                    s.duracao as servico_duracao
                FROM agendamentos a
                JOIN barbeiros b ON a.barbeiro_id = b.id
                JOIN servicos s ON a.servico_id = s.id
                WHERE a.usuario_id = ?
            ";
            
            $params = [$usuario_id];
            
            if ($status) {
                $sql .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function cancelar($id, $usuario_id) {
        try {
            // Verificar se o agendamento pertence ao usuário
            $stmt = $this->conn->prepare("SELECT * FROM agendamentos WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuario_id]);
            $agendamento = $stmt->fetch();
            
            if (!$agendamento) {
                return ['success' => false, 'message' => 'Agendamento não encontrado!'];
            }
            
            if ($agendamento['status'] === 'cancelado') {
                return ['success' => false, 'message' => 'Agendamento já está cancelado!'];
            }
            
            // Verificar se pode cancelar (pelo menos 2 horas antes)
            $dataHoraAgendamento = strtotime($agendamento['data_agendamento'] . ' ' . $agendamento['hora_agendamento']);
            $agora = time();
            $duasHoras = 2 * 60 * 60; // 2 horas em segundos
            
            if ($dataHoraAgendamento - $agora < $duasHoras) {
                return ['success' => false, 'message' => 'Cancelamento deve ser feito com pelo menos 2 horas de antecedência!'];
            }
            
            // Cancelar o agendamento
            $stmt = $this->conn->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = ?");
            
            if ($stmt->execute([$id])) {
                return ['success' => true, 'message' => 'Agendamento cancelado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao cancelar agendamento!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
}

// models/Barbeiro.php
class Barbeiro {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function listarAtivos() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM barbeiros WHERE status = 'ativo' ORDER BY nome");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function obterPorId($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM barbeiros WHERE id = ? AND status = 'ativo'");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function verificarDisponibilidade($barbeiro_id, $data) {
        try {
            $barbeiro = $this->obterPorId($barbeiro_id);
            if (!$barbeiro) {
                return false;
            }
            
            // Verificar se o barbeiro trabalha no dia da semana
            $diaSemana = date('N', strtotime($data)); // 1 (segunda) a 7 (domingo)
            $diasTrabalho = explode(',', $barbeiro['dias_trabalho']);
            
            return in_array($diaSemana, $diasTrabalho);
        } catch (Exception $e) {
            return false;
        }
    }
}