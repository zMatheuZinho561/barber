<?php
require_once '../models/Servico.php';

class ServicoController {
    private $servico;
    
    public function __construct() {
        $this->servico = new Servico();
    }
    
    public function listar() {
        try {
            $servicos = $this->servico->listarTodos();
            return [
                'success' => true,
                'data' => $servicos
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao listar serviços: ' . $e->getMessage()
            ];
        }
    }
    
    public function obter($id) {
        try {
            if (!$id) {
                throw new Exception('ID do serviço é obrigatório');
            }
            
            $servico = $this->servico->obterPorId($id);
            
            if (!$servico) {
                throw new Exception('Serviço não encontrado');
            }
            
            return [
                'success' => true,
                'data' => $servico
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function criar($dados) {
        try {
            // Validações
            if (empty($dados['nome'])) {
                throw new Exception('Nome do serviço é obrigatório');
            }
            
            if (empty($dados['preco']) || $dados['preco'] <= 0) {
                throw new Exception('Preço deve ser maior que zero');
            }
            
            if (empty($dados['duracao']) || $dados['duracao'] <= 0) {
                throw new Exception('Duração deve ser maior que zero');
            }
            
            return $this->servico->criar($dados);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function atualizar($id, $dados) {
        try {
            if (!$id) {
                throw new Exception('ID do serviço é obrigatório');
            }
            
            // Verificar se serviço existe
            $servicoExistente = $this->servico->obterPorId($id);
            if (!$servicoExistente) {
                throw new Exception('Serviço não encontrado');
            }
            
            // Validações
            if (empty($dados['nome'])) {
                throw new Exception('Nome do serviço é obrigatório');
            }
            
            if (empty($dados['preco']) || $dados['preco'] <= 0) {
                throw new Exception('Preço deve ser maior que zero');
            }
            
            if (empty($dados['duracao']) || $dados['duracao'] <= 0) {
                throw new Exception('Duração deve ser maior que zero');
            }
            
            return $this->servico->atualizar($id, $dados);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function excluir($id) {
        try {
            if (!$id) {
                throw new Exception('ID do serviço é obrigatório');
            }
            
            // Verificar se serviço existe
            $servicoExistente = $this->servico->obterPorId($id);
            if (!$servicoExistente) {
                throw new Exception('Serviço não encontrado');
            }
            
            return $this->servico->excluir($id);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function processarRequisicao() {
        header('Content-Type: application/json');
        
        try {
            $metodo = $_SERVER['REQUEST_METHOD'];
            $acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
            
            switch ($metodo) {
                case 'GET':
                    if ($acao === 'obter' && isset($_GET['id'])) {
                        $resultado = $this->obter($_GET['id']);
                    } else {
                        $resultado = $this->listar();
                    }
                    break;
                    
                case 'POST':
                    switch ($acao) {
                        case 'criar':
                            $dados = [
                                'nome' => $_POST['nome'] ?? '',
                                'descricao' => $_POST['descricao'] ?? '',
                                'preco' => (float)($_POST['preco'] ?? 0),
                                'duracao' => (int)($_POST['duracao'] ?? 0),
                                'imagem' => $_POST['imagem'] ?? null
                            ];
                            $resultado = $this->criar($dados);
                            break;
                            
                        case 'atualizar':
                            $id = $_POST['id'] ?? '';
                            $dados = [
                                'nome' => $_POST['nome'] ?? '',
                                'descricao' => $_POST['descricao'] ?? '',
                                'preco' => (float)($_POST['preco'] ?? 0),
                                'duracao' => (int)($_POST['duracao'] ?? 0),
                                'imagem' => $_POST['imagem'] ?? null
                            ];
                            $resultado = $this->atualizar($id, $dados);
                            break;
                            
                        case 'excluir':
                            $id = $_POST['id'] ?? '';
                            $resultado = $this->excluir($id);
                            break;
                            
                        default:
                            throw new Exception('Ação não reconhecida');
                    }
                    break;
                    
                default:
                    throw new Exception('Método não permitido');
            }
            
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

// Se este arquivo for chamado diretamente, processar a requisição
if (basename($_SERVER['SCRIPT_NAME']) === basename(__FILE__)) {
    $controller = new ServicoController();
    $controller->processarRequisicao();
}
?>