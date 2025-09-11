<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'barbearia_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}

// Função auxiliar para obter conexão
function getConnection() {
    $database = new Database();
    return $database->connect();
}
?>