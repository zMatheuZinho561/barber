<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'barbearia_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}

// Classe para operações de usuário
class Usuario {
    private $conn;
    private $table = 'usuarios';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Registrar novo usuário
    public function registrar($nome, $email, $senha, $telefone) {
        $query = "INSERT INTO " . $this->table . " (nome, email, senha, telefone) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        return $stmt->execute([$nome, $email, $senha_hash, $telefone]);
    }
    
    // Login do usuário
    public function login($email, $senha) {
        $query = "SELECT id, nome, email, senha, tipo_usuario FROM " . $this->table . " WHERE email = ? AND ativo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            if(password_verify($senha, $row['senha'])) {
                // Atualizar último login
                $this->atualizarUltimoLogin($row['id']);
                return $row;
            }
        }
        
        return false;
    }
    
    // Verificar se email já existe
    public function emailExiste($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        return $stmt->rowCount() > 0;
    }
    
    // Atualizar último login
    private function atualizarUltimoLogin($id) {
        $query = "UPDATE " . $this->table . " SET ultimo_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
    }
}
?>