<?php
// Só inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

class Auth {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function register($nome, $email, $telefone, $senha) {
        try {
            // Verificar se email já existe
            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email já cadastrado!'];
            }
            
            // Validações básicas
            if (empty($nome) || empty($email) || empty($telefone) || empty($senha)) {
                return ['success' => false, 'message' => 'Todos os campos são obrigatórios!'];
            }
            
            if (strlen($senha) < 6) {
                return ['success' => false, 'message' => 'A senha deve ter pelo menos 6 caracteres!'];
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Email inválido!'];
            }
            
            // Hash da senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            // Inserir usuário
            $stmt = $this->conn->prepare("INSERT INTO usuarios (nome, email, telefone, senha) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $email, $telefone, $senha_hash]);
            
            return ['success' => true, 'message' => 'Cadastro realizado com sucesso!'];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro no cadastro: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $senha) {
        try {
            $stmt = $this->conn->prepare("SELECT id, nome, email, senha, tipo_usuario FROM usuarios WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Email ou senha incorretos!'];
            }
            
            $usuario = $stmt->fetch();
            
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];
                $_SESSION['logged_in'] = true;
                
                return ['success' => true, 'message' => 'Login realizado com sucesso!', 'tipo_usuario' => $usuario['tipo_usuario']];
            } else {
                return ['success' => false, 'message' => 'Email ou senha incorretos!'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erro no login: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        session_destroy();
        header("Location: /newbarber/index.php");
        exit();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isAdmin() {
        return isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header("Location: ../admin/admin_dashboard.php");
            exit();
        }
    }
}
?>