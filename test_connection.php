<?php
// test_connection.php
header('Content-Type: application/json');

try {
    // Incluir configuração do banco
    require_once 'config/database.php';
    
    echo "<h1>🔧 Teste de Conexão - BarberShop Elite</h1>";
    
    // Testar conexão
    echo "<h2>1. Testando Conexão com Banco:</h2>";
    $conexaoTeste = testarConexao();
    
    if ($conexaoTeste['success']) {
        echo "✅ " . $conexaoTeste['message'] . "<br>";
        
        // Testar estrutura do banco
        echo "<h2>2. Verificando Estrutura do Banco:</h2>";
        $pdo = getConnection();
        
        // Verificar tabelas existentes
        $stmt = $pdo->query("SHOW TABLES");
        $tabelas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h3>Tabelas encontradas:</h3>";
        if (empty($tabelas)) {
            echo "❌ Nenhuma tabela encontrada<br>";
            echo "<p style='color: orange;'>⚠️ Execute o script SQL para criar as tabelas necessárias</p>";
        } else {
            foreach ($tabelas as $tabela) {
                echo "📋 " . $tabela . "<br>";
            }
        }
        
        // Verificar tabela usuarios especificamente
        if (in_array('usuarios', $tabelas)) {
            echo "<h3>Estrutura da tabela 'usuarios':</h3>";
            $stmt = $pdo->query("DESCRIBE usuarios");
            $campos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Padrão</th></tr>";
            foreach ($campos as $campo) {
                echo "<tr>";
                echo "<td>" . $campo['Field'] . "</td>";
                echo "<td>" . $campo['Type'] . "</td>";
                echo "<td>" . $campo['Null'] . "</td>";
                echo "<td>" . ($campo['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Contar usuários
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
            $total = $stmt->fetch()['total'];
            echo "<p>👥 Total de usuários cadastrados: <strong>$total</strong></p>";
        }
        
    } else {
        echo "❌ " . $conexaoTeste['message'] . "<br>";
    }
    
    echo "<h2>3. Testando APIs:</h2>";
    
    // Testar se arquivos da API existem
    $arquivosAPI = [
        'api/usuarios.php' => 'API de Usuários',
        'api/agendamentos.php' => 'API de Agendamentos',
        'models/Usuario.php' => 'Modelo Usuario'
    ];
    
    foreach ($arquivosAPI as $arquivo => $nome) {
        if (file_exists($arquivo)) {
            echo "✅ $nome ($arquivo) - Encontrado<br>";
        } else {
            echo "❌ $nome ($arquivo) - Não encontrado<br>";
        }
    }
    
    echo "<h2>4. Configurações PHP:</h2>";
    echo "🔧 Versão PHP: " . PHP_VERSION . "<br>";
    echo "🔧 PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Habilitado' : '❌ Desabilitado') . "<br>";
    echo "🔧 Sessions: " . (extension_loaded('session') ? '✅ Habilitado' : '❌ Desabilitado') . "<br>";
    
    echo "<h2>5. Instruções:</h2>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007bff;'>";
    echo "<h3>Para configurar o sistema:</h3>";
    echo "<ol>";
    echo "<li>📊 Execute o script SQL no phpMyAdmin ou MySQL Workbench</li>";
    echo "<li>🔧 Verifique as configurações do banco em <code>config/database.php</code></li>";
    echo "<li>📁 Certifique-se que os arquivos da API estão na pasta <code>api/</code></li>";
    echo "<li>🌐 Teste o login no sistema</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<style>";
    echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }";
    echo "h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }";
    echo "h2 { color: #007bff; margin-top: 30px; }";
    echo "h3 { color: #666; }";
    echo "table { margin: 10px 0; border-collapse: collapse; width: 100%; }";
    echo "th { background: #007bff; color: white; }";
    echo "td, th { padding: 8px; text-align: left; }";
    echo "code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }";
    echo "</style>";
    
} catch (Exception $e) {
    echo "<h1>❌ Erro no Teste</h1>";
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
    echo "<p>Verifique:</p>";
    echo "<ul>";
    echo "<li>Se o arquivo <code>config/database.php</code> existe</li>";
    echo "<li>Se as configurações do banco estão corretas</li>";
    echo "<li>Se o MySQL está rodando</li>";
    echo "<li>Se o banco de dados foi criado</li>";
    echo "</ul>";
}
?>