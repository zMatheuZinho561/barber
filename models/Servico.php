<?php
require_once 'config/database.php';

class Servico {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function listarTodos($status = 'ativo') {
        try {
            $sql = "SELECT * FROM servicos";
            $params = [];
            
            if ($status) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY nome";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function obterPorId($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM servicos WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function criar($dados) {
        try {
            $sql = "INSERT INTO servicos (nome, descricao, preco, duracao, imagem) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['duracao'],
                $dados['imagem'] ?? null
            ])) {
                return ['success' => true, 'message' => 'Serviço criado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao criar serviço!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function atualizar($id, $dados) {
        try {
            $sql = "UPDATE servicos SET nome = ?, descricao = ?, preco = ?, duracao = ?, imagem = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['duracao'],
                $dados['imagem'] ?? null,
                $id
            ])) {
                return ['success' => true, 'message' => 'Serviço atualizado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao atualizar serviço!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function excluir($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE servicos SET status = 'inativo' WHERE id = ?");
            
            if ($stmt->execute([$id])) {
                return ['success' => true, 'message' => 'Serviço removido com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao remover serviço!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
}
?>