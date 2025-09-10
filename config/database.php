<?php
// config/database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'barbearia_system';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Erro de conexão: " . $exception->getMessage());
            die("Erro de conexão com o banco de dados");
        }
        return $this->conn;
    }
}

// Função auxiliar para iniciar sessão
function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Função para verificar se usuário está logado
function usuarioLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']);
}

// Função para verificar se é admin
function isAdmin() {
    iniciarSessao();
    return isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
}

// Função para redirecionar se não estiver logado
function verificarLogin() {
    if (!usuarioLogado()) {
        header('Location: login.php');
        exit();
    }
}

// Função para verificar admin
function verificarAdmin() {
    verificarLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}
?>