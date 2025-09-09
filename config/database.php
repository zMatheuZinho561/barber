<?php
// config/database.php
class Database {
    private static $instance = null;
    private $connection;
    
    // Configurações do banco de dados
    private $host = 'localhost';
    private $port = '3306';
    private $database = 'barbershop';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            throw new Exception('Erro na conexão com banco de dados. Verifique as configurações.');
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Métodos auxiliares para facilitar operações
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage() . " | SQL: " . $sql);
            throw new Exception('Erro ao executar operação no banco de dados');
        }
    }
    
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
    
    public function inTransaction() {
        return $this->connection->inTransaction();
    }
    
    // Método para executar transações com segurança
    public function transaction(callable $callback) {
        $this->beginTransaction();
        
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    // Método para verificar se a conexão está ativa
    public function isConnected() {
        try {
            $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Método para reconectar se necessário
    public function reconnect() {
        $this->connection = null;
        $this->connect();
    }
    
    // Método para executar múltiplas queries (útil para migrations)
    public function multiQuery($sql) {
        $queries = explode(';', $sql);
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $this->connection->exec($query);
            }
        }
    }
    
    // Método para escapar valores (embora prepared statements sejam preferíveis)
    public function quote($value) {
        return $this->connection->quote($value);
    }
    
    // Método para obter informações do banco
    public function getDatabaseInfo() {
        $info = [
            'server_version' => $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION),
            'client_version' => $this->connection->getAttribute(PDO::ATTR_CLIENT_VERSION),
            'connection_status' => $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS),
            'server_info' => $this->connection->getAttribute(PDO::ATTR_SERVER_INFO)
        ];
        
        return $info;
    }
    
    // Prevenir clonagem
    public function __clone() {
        throw new Exception('Cannot clone singleton Database instance');
    }
    
    // Prevenir deserialização
    public function __wakeup() {
        throw new Exception('Cannot unserialize singleton Database instance');
    }
    
    // Destructor para fechar conexão
    public function __destruct() {
        $this->connection = null;
    }
}