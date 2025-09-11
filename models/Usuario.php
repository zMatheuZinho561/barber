<?php
require_once '../config/database.php';

class Usuario {
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    public function registrar($dados) {
        try {
            // Verificar se email já existe
            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$dados['email']]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email já cadastrado!'];
            }
            
            // Hash da senha
            $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([$dados['nome'], $dados['email'], $dados['telefone'], $senhaHash])) {
                return ['success' => true, 'message' => 'Usuário cadastrado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao cadastrar usuário!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $senha) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND status = 'ativo'");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Iniciar sessão
                session_start();
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
                $_SESSION['logado'] = true;
                
                return ['success' => true, 'message' => 'Login realizado com sucesso!', 'usuario' => $usuario];
            }
            
            return ['success' => false, 'message' => 'Email ou senha incorretos!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
    }
    
    public function obterPorId($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function atualizarPerfil($id, $dados) {
        try {
            $sql = "UPDATE usuarios SET nome = ?, telefone = ?";
            $params = [$dados['nome'], $dados['telefone']];
            
            // Se uma nova senha foi fornecida
            if (!empty($dados['senha'])) {
                $sql .= ", senha = ?";
                $params[] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $id;
            
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute($params)) {
                return ['success' => true, 'message' => 'Perfil atualizado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao atualizar perfil!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function listarTodos() {
        try {
            $stmt = $this->conn->prepare("SELECT id, nome, email, telefone, tipo_usuario, data_cadastro, status FROM usuarios ORDER BY data_cadastro DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function alterarStatus($id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE usuarios SET status = ? WHERE id = ?");
            
            if ($stmt->execute([$status, $id])) {
                return ['success' => true, 'message' => 'Status alterado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao alterar status!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
}
?>