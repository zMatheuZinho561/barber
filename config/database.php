<?php
// config/database.php

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'barbearia_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
        throw new Exception("Erro de conexão com o banco de dados");
    }
}

// Função para testar a conexão
function testarConexao() {
    try {
        $pdo = getConnection();
        return ['success' => true, 'message' => 'Conexão bem-sucedida!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Função para inicializar o banco (criar tabelas se não existirem)
function inicializarBanco() {
    try {
        $pdo = getConnection();
        
        // Verificar se as tabelas principais existem
        $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
        if ($stmt->rowCount() === 0) {
            // Executar script SQL de criação
            $sql = file_get_contents(__DIR__ . '/../barbearia_system.sql');
            if ($sql) {
                $pdo->exec($sql);
                return ['success' => true, 'message' => 'Banco de dados inicializado com sucesso!'];
            }
        }
        
        return ['success' => true, 'message' => 'Banco de dados já está configurado.'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erro ao inicializar banco: ' . $e->getMessage()];
    }
}
?>