<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = getConnection();
        } catch (Exception $e) {
            throw new Exception("Erro ao conectar com banco de dados: " . $e->getMessage());
        }
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
            
            // Tipo de usuário padrão
            $tipoUsuario = isset($dados['tipo_usuario']) ? $dados['tipo_usuario'] : 'cliente';
            
            $sql = "INSERT INTO usuarios (nome, email, telefone, senha, tipo_usuario) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            if ($stmt->execute([
                $dados['nome'], 
                $dados['email'], 
                $dados['telefone'], 
                $senhaHash, 
                $tipoUsuario
            ])) {
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
                // Iniciar sessão se ainda não foi iniciada
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
                $_SESSION['logado'] = true;
                
                // Retornar dados do usuário sem a senha
                $usuarioRetorno = [
                    'id' => $usuario['id'],
                    'nome' => $usuario['nome'],
                    'email' => $usuario['email'],
                    'tipo_usuario' => $usuario['tipo_usuario']
                ];
                
                return [
                    'success' => true, 
                    'message' => 'Login realizado com sucesso!', 
                    'usuario' => $usuarioRetorno
                ];
            }
            
            return ['success' => false, 'message' => 'Email ou senha incorretos!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro no login: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            session_unset();
            session_destroy();
            
            return ['success' => true, 'message' => 'Logout realizado com sucesso!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro no logout: ' . $e->getMessage()];
        }
    }
    
    public function obterPorId($id) {
        try {
            $stmt = $this->conn->prepare("SELECT id, nome, email, telefone, tipo_usuario, status, data_cadastro FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function atualizarPerfil($id, $dados) {
        try {
            $campos = [];
            $params = [];
            
            // Campos básicos
            if (isset($dados['nome'])) {
                $campos[] = "nome = ?";
                $params[] = $dados['nome'];
            }
            
            if (isset($dados['telefone'])) {
                $campos[] = "telefone = ?";
                $params[] = $dados['telefone'];
            }
            
            if (isset($dados['tipo_usuario'])) {
                $campos[] = "tipo_usuario = ?";
                $params[] = $dados['tipo_usuario'];
            }
            
            // Se uma nova senha foi fornecida
            if (!empty($dados['senha'])) {
                $campos[] = "senha = ?";
                $params[] = password_hash($dados['senha'], PASSWORD_DEFAULT);
            }
            
            if (empty($campos)) {
                return ['success' => false, 'message' => 'Nenhum campo para atualizar!'];
            }
            
            $params[] = $id;
            $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function alterarStatus($id, $status) {
        try {
            if (!in_array($status, ['ativo', 'inativo'])) {
                return ['success' => false, 'message' => 'Status inválido!'];
            }
            
            $stmt = $this->conn->prepare("UPDATE usuarios SET status = ? WHERE id = ?");
            
            if ($stmt->execute([$status, $id])) {
                return ['success' => true, 'message' => 'Status alterado com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao alterar status!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function buscarPorEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT id, nome, email, telefone, tipo_usuario, status FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function contarUsuarios($tipo = null) {
        try {
            if ($tipo) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usuarios WHERE tipo_usuario = ? AND status = 'ativo'");
                $stmt->execute([$tipo]);
            } else {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'");
                $stmt->execute();
            }
            
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    public function verificarSenha($id, $senha) {
        try {
            $stmt = $this->conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function alterarSenha($id, $novaSenha) {
        try {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            
            if ($stmt->execute([$senhaHash, $id])) {
                return ['success' => true, 'message' => 'Senha alterada com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao alterar senha!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
    
    public function excluir($id) {
        try {
            // Inativar usuário em vez de excluir
            $stmt = $this->conn->prepare("UPDATE usuarios SET status = 'inativo' WHERE id = ?");
            
            if ($stmt->execute([$id])) {
                return ['success' => true, 'message' => 'Usuário removido com sucesso!'];
            }
            
            return ['success' => false, 'message' => 'Erro ao remover usuário!'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }
}
?>