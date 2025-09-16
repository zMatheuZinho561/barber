<?php
// teste_conexao.php - Script para diagnosticar problemas de conexão

// Configurações do banco de dados
$config = [
    'host' => 'localhost',
    'dbname' => 'barbearia_system',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

echo "<h2>🔍 Diagnóstico de Conexão - Sistema Barbearia</h2>";
echo "<hr>";

// 1. Verificar se a extensão PDO está disponível
echo "<h3>1. Verificando extensões PHP</h3>";
if (extension_loaded('pdo')) {
    echo "✅ PDO: Disponível<br>";
} else {
    echo "❌ PDO: NÃO DISPONÍVEL - Instale a extensão php-pdo<br>";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL: Disponível<br>";
} else {
    echo "❌ PDO MySQL: NÃO DISPONÍVEL - Instale a extensão php-pdo-mysql<br>";
}

echo "<br>";

// 2. Testar conexão básica com MySQL
echo "<h3>2. Testando conexão com MySQL</h3>";
try {
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Conexão com servidor MySQL: SUCESSO<br>";
    
    // Verificar versão do MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "📋 Versão do MySQL: {$version}<br>";
    
} catch (PDOException $e) {
    echo "❌ Erro ao conectar com MySQL: " . $e->getMessage() . "<br>";
    echo "<strong>Possíveis soluções:</strong><br>";
    echo "- Verifique se o XAMPP/WAMP está rodando<br>";
    echo "- Verifique se o serviço MySQL está ativo<br>";
    echo "- Verifique as credenciais (usuário/senha)<br>";
    echo "<br>";
}

echo "<br>";

// 3. Verificar se o banco existe
echo "<h3>3. Verificando banco de dados</h3>";
try {
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Verificar se o banco existe
    $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
    $stmt->execute([$config['dbname']]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Banco 'barbearia_system': EXISTE<br>";
        
        // Conectar ao banco específico
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "✅ Conexão com banco 'barbearia_system': SUCESSO<br>";
        
    } else {
        echo "❌ Banco 'barbearia_system': NÃO EXISTE<br>";
        echo "<strong>Criando banco de dados...</strong><br>";
        
        $pdo->exec("CREATE DATABASE barbearia_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Banco 'barbearia_system' criado com sucesso!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro ao verificar/criar banco: " . $e->getMessage() . "<br>";
}

echo "<br>";

// 4. Verificar tabelas
echo "<h3>4. Verificando estrutura do banco</h3>";
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $tabelas_necessarias = ['usuarios', 'servicos', 'agendamentos', 'barbeiros'];
    $tabelas_existentes = [];
    
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tabelas_existentes[] = $row[0];
    }
    
    foreach ($tabelas_necessarias as $tabela) {
        if (in_array($tabela, $tabelas_existentes)) {
            echo "✅ Tabela '{$tabela}': EXISTE<br>";
        } else {
            echo "❌ Tabela '{$tabela}': NÃO EXISTE<br>";
        }
    }
    
    if (empty($tabelas_existentes)) {
        echo "<br><strong>⚠️ Nenhuma tabela encontrada! Execute o script SQL para criar as tabelas.</strong><br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro ao verificar tabelas: " . $e->getMessage() . "<br>";
}

echo "<br>";

// 5. Criar estrutura básica se não existir
echo "<h3>5. Criando estrutura básica (se necessário)</h3>";
try {
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Criar tabela usuarios se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            telefone VARCHAR(20),
            senha VARCHAR(255) NOT NULL,
            tipo_usuario ENUM('cliente', 'barbeiro', 'admin') DEFAULT 'cliente',
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Criar tabela servicos se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS servicos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            descricao TEXT,
            preco DECIMAL(10,2) NOT NULL,
            duracao INT NOT NULL COMMENT 'Duração em minutos',
            imagem VARCHAR(255),
            status ENUM('ativo', 'inativo') DEFAULT 'ativo',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Criar tabela agendamentos se não existir
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agendamentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            barbeiro_id INT NOT NULL,
            servico_id INT NOT NULL,
            data_agendamento DATE NOT NULL,
            horario TIME NOT NULL,
            observacoes TEXT,
            status ENUM('agendado', 'confirmado', 'em_andamento', 'concluido', 'cancelado', 'rejeitado') DEFAULT 'agendado',
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
            FOREIGN KEY (barbeiro_id) REFERENCES usuarios(id),
            FOREIGN KEY (servico_id) REFERENCES servicos(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    echo "✅ Estrutura básica criada/verificada com sucesso!<br>";
    
    // Inserir dados de exemplo se não existirem
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        echo "<br><strong>Inserindo dados de exemplo...</strong><br>";
        
        // Usuário admin padrão
        $pdo->exec("
            INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES 
            ('Administrador', 'admin@barbearia.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'admin')
        ");
        
        // Serviços padrão
        $pdo->exec("
            INSERT INTO servicos (nome, descricao, preco, duracao) VALUES 
            ('Corte Clássico', 'Corte tradicional com acabamento impecável', 45.00, 45),
            ('Barba + Bigode', 'Aparagem e modelagem profissional', 35.00, 30),
            ('Pacote Completo', 'Corte + Barba + Sobrancelha', 75.00, 90)
        ");
        
        echo "✅ Dados de exemplo inseridos!<br>";
        echo "📋 Login de teste: admin@barbearia.com / 123456<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro ao criar estrutura: " . $e->getMessage() . "<br>";
}

echo "<br>";

// 6. Teste final da função getConnection()
echo "<h3>6. Testando função getConnection()</h3>";
try {
    function getConnection() {
        $dsn = "mysql:host=localhost;dbname=barbearia_system;charset=utf8mb4";
        $pdo = new PDO($dsn, 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    }
    
    $pdo = getConnection();
    echo "✅ Função getConnection(): FUNCIONANDO<br>";
    
    // Teste de login
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute(['admin@barbearia.com']);
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        echo "✅ Teste de consulta: SUCESSO<br>";
        echo "📋 Usuário encontrado: " . $usuario['nome'] . "<br>";
    } else {
        echo "⚠️ Nenhum usuário encontrado para teste<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro na função getConnection(): " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>🎯 Resumo</h3>";
echo "<p>Execute este script para diagnosticar e corrigir problemas de conexão.<br>";
echo "Se tudo estiver ✅, seu sistema deve funcionar corretamente!</p>";
?>