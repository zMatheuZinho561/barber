<?php
require_once '../config/database.php';
verificarLogin();

$database = new Database();
$conn = $database->getConnection();

// Buscar agendamentos do usuário
$query = "SELECT a.*, s.nome as servico_nome, s.preco, b.nome as barbeiro_nome 
          FROM agendamentos a
          JOIN servicos s ON a.servico_id = s.id
          JOIN barbeiros b ON a.barbeiro_id = b.id
          WHERE a.cliente_id = ?
          ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['usuario_id']]);
$agendamentos = $stmt->fetchAll();

// Processar cancelamento
if (isset($_POST['cancelar_agendamento'])) {
    $agendamento_id = $_POST['agendamento_id'];
    
    // Verificar se o agendamento pertence ao usuário e pode ser cancelado
    $query = "UPDATE agendamentos SET status = 'cancelado' 
              WHERE id = ? AND cliente_id = ? AND status = 'agendado' 
              AND CONCAT(data_agendamento, ' ', hora_agendamento) > NOW()";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$agendamento_id, $_SESSION['usuario_id']])) {
        $sucesso = "Agendamento cancelado com sucesso!";
    } else {
        $erro = "Não foi possível cancelar o agendamento.";
    }
    
    // Recarregar agendamentos
    header('Location: perfil.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Barbearia Premium</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .btn-primary {
            background: #3498db;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .welcome-card h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .welcome-card p {
            color: #666;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .action-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .action-card:hover {
            transform: translateY(-3px);
        }
        
        .action-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #27ae60;
        }
        
        .btn-success:hover {
            background: #229954;
        }
        
        .agendamentos-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .agendamentos-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        
        .agendamento-card {
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: box-shadow 0.3s;
        }
        
        .agendamento-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .agendamento-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .agendamento-info h4 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .agendamento-detalhes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .detalhe {
            display: flex;
            flex-direction: column;
        }
        
        .detalhe-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .detalhe-valor {
            font-weight: 500;
            color: #333;
        }
        
        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-agendado {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-confirmado {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .status-concluido {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .status-cancelado {
            background: #ffebee;
            color: #c62828;
        }
        
        .btn-cancelar {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-cancelar:hover {
            background: #c0392b;
        }
        
        .empty-state {
            text-align: center;
            color: #666;
            padding: 3rem;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
            
            .agendamento-header {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">✂️ Barbearia Premium</div>
            <div class="nav-links">
                <a href="index.php">Início</a>
                <a href="perfil.php">Meu Perfil</a>
                <a href="agendamento.php" class="btn-primary">Agendar</a>
                <span>Olá, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>
            <p>Gerencie seus agendamentos e perfil aqui.</p>
        </div>

        <div class="quick-actions">
            <div class="action-card">
                <h3>📅 Novo Agendamento</h3>
                <p>Agende um novo horário com nossos profissionais</p>
                <a href="../models/agendamento.php" class="btn">Agendar Agora</a>
            </div>
            
            <div class="action-card">
                <h3>✂️ Nossos Serviços</h3>
                <p>Conheça todos os serviços disponíveis</p>
                <a href="index.php#servicos" class="btn btn-success">Ver Serviços</a>
            </div>
        </div>

        <div class="agendamentos-section">
            <h2>Meus Agendamentos</h2>
            
            <?php if (empty($agendamentos)): ?>
                <div class="empty-state">
                    <h3>Nenhum agendamento encontrado</h3>
                    <p>Que tal fazer seu primeiro agendamento?</p>
                    <a href="../models/agendamento.php" class="btn" style="margin-top: 1rem;">Agendar Agora</a>
                </div>
            <?php else: ?>
                <?php foreach ($agendamentos as $agendamento): ?>
                    <div class="agendamento-card">
                        <div class="agendamento-header">
                            <div class="agendamento-info">
                                <h4><?php echo htmlspecialchars($agendamento['servico_nome']); ?></h4>
                                <span class="status status-<?php echo $agendamento['status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'agendado' => 'Agendado',
                                        'confirmado' => 'Confirmado',
                                        'concluido' => 'Concluído',
                                        'cancelado' => 'Cancelado'
                                    ];
                                    echo $status_text[$agendamento['status']];
                                    ?>
                                </span>
                            </div>
                            
                            <?php if ($agendamento['status'] == 'agendado' && 
                                      strtotime($agendamento['data_agendamento'] . ' ' . $agendamento['hora_agendamento']) > time()): ?>
                                <form method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                    <input type="hidden" name="agendamento_id" value="<?php echo $agendamento['id']; ?>">
                                    <button type="submit" name="cancelar_agendamento" class="btn-cancelar">
                                        Cancelar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        
                        <div class="agendamento-detalhes">
                            <div class="detalhe">
                                <span class="detalhe-label">Data</span>
                                <span class="detalhe-valor">
                                    <?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?>
                                </span>
                            </div>
                            
                            <div class="detalhe">
                                <span class="detalhe-label">Horário</span>
                                <span class="detalhe-valor">
                                    <?php echo date('H:i', strtotime($agendamento['hora_agendamento'])); ?>
                                </span>
                            </div>
                            
                            <div class="detalhe">
                                <span class="detalhe-label">Barbeiro</span>
                                <span class="detalhe-valor">
                                    <?php echo htmlspecialchars($agendamento['barbeiro_nome']); ?>
                                </span>
                            </div>
                            
                            <div class="detalhe">
                                <span class="detalhe-label">Valor</span>
                                <span class="detalhe-valor">
                                    R$ <?php echo number_format($agendamento['valor'] ?: $agendamento['preco'], 2, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($agendamento['observacoes']): ?>
                            <div class="detalhe">
                                <span class="detalhe-label">Observações</span>
                                <span class="detalhe-valor">
                                    <?php echo htmlspecialchars($agendamento['observacoes']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>