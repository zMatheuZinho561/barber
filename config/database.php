<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'barbeararia_sistema';
    private $username = 'root'; // Altere conforme sua configuração
    private $password = '';     // Altere conforme sua configuração
    private $charset = 'utf8mb4';
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            exit();
        }
        
        return $this->conn;
    }
}

// Função auxiliar para conectar ao banco
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>