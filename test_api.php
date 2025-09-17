<?php
echo "<h2>Teste da API de Agendamento</h2>";

// Teste 1: Verificar se o arquivo existe
if (file_exists('api/agendamento.php')) {
    echo "✅ Arquivo api/agendamento.php existe<br>";
} else {
    echo "❌ Arquivo api/agendamento.php NÃO existe<br>";
    echo "Crie a pasta 'api' na raiz e coloque o arquivo lá<br>";
}

// Teste 2: Verificar se a pasta api existe
if (is_dir('api')) {
    echo "✅ Pasta 'api' existe<br>";
} else {
    echo "❌ Pasta 'api' NÃO existe<br>";
}

// Teste 3: Verificar arquivos necessários
$files = [
    'include/auth.php',
    'config/database.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file existe<br>";
    } else {
        echo "❌ $file NÃO existe<br>";
    }
}

// Teste 4: Testar conexão com banco
try {
    if (file_exists('include/auth.php')) {
        session_start();
        require_once 'include/auth.php';
        $conn = getDBConnection();
        echo "✅ Conexão com banco OK<br>";
        
        // Verificar tabela servicos
        $stmt = $conn->query("SELECT COUNT(*) as count FROM servicos");
        $count = $stmt->fetch()['count'];
        echo "✅ Tabela servicos tem $count registros<br>";
        
    } else {
        echo "❌ Não foi possível testar o banco (arquivo auth.php não encontrado)<br>";
    }
} catch (Exception $e) {
    echo "❌ Erro no banco: " . $e->getMessage() . "<br>";
}

// Teste 5: Simular chamada da API
echo "<hr><h3>Testando API diretamente:</h3>";
if (file_exists('api/agendamento.php')) {
    echo "<p>Fazendo requisição para api/agendamento.php?action=get_servicos</p>";
    
    // Capturar output
    ob_start();
    $_GET['action'] = 'get_servicos';
    include 'api/agendamento.php';
    $output = ob_get_clean();
    
    echo "<strong>Resposta da API:</strong><br>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
    if (json_decode($output) !== null) {
        echo "✅ JSON válido<br>";
    } else {
        echo "❌ JSON inválido<br>";
        echo "Erro JSON: " . json_last_error_msg() . "<br>";
    }
}
?>