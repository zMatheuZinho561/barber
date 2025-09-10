<?php
require_once '../config/database.php';
verificarAdmin();

$database = new Database();
$conn = $database->getConnection();

// Estatísticas do dashboard
$stats = [];

// Total de agendamentos
$query = "SELECT COUNT(*) as total FROM agendamentos";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_agendamentos'] = $stmt->fetch()['total'];

// Agendamentos hoje
$query = "SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE()";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['agendamentos_hoje'] = $stmt->fetch()['total'];

// Total de clientes
$query = "SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'cliente'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['total_clientes'] = $stmt->fetch()['total'];

// Receita do mês
$query = "SELECT COALESCE(SUM(valor), 0) as receita FROM agendamentos 
          WHERE MONTH(data_agendamento) = MONTH(CURDATE()) 
          AND YEAR(data_agendamento) = YEAR(CURDATE())
          AND status = 'concluido'";
$stmt = $conn->prepare($query);
$stmt->execute();
$stats['receita_mes'] = $stmt->fetch()['receita'];

// Agendamentos recentes
$query = "SELECT a.*, u.nome as cliente_nome, s.nome as servico_nome, b.nome as barbeiro_nome
          FROM agendamentos a
          JOIN usuarios u ON a.cliente_id = u.id
          JOIN servicos s ON a.servico_id = s.id
          JOIN barbeiros b ON a.barbeiro_id = b.id
          ORDER BY a.data_criacao DESC
          LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();
$agendamentos_recentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Barbearia Premium</title>
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
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 1rem;
        }
        
        .sidebar h2 {
            margin-bottom: 2rem;
            text-align: center;
            color: #ecf0f1;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-menu a {
            color: #ecf0f1;
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #34495e;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        
        .header {
            background: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-info a {
            color: #e74c3c;
            text-decoration: none;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.85rem;
        }
        
        .recent-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .recent-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .table th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
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
        
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .table {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>📊 Admin Panel</h2>
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="agendamentos.php">Agendamentos</a></li>
                <li><a href="barbeiros.php">Barbeiros</a></li>
                <li><a href="servicos.php">Serviços</a></li>
                <li><a href="clientes.php">Clientes</a></li>
                <li><a href="horarios.php">Horários</a></li>
                <li><a href="../index.php">Ver Site</a></li>
                <li><a href="../logout.php">Sair</a></li>
            </ul>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?></span>
                    <a href="../logout.php">Sair</a>
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total de Agendamentos</h3>
                    <div class="stat-value"><?php echo number_format($stats['total_agendamentos']); ?></div>
                    <div class="stat-label">Todos os tempos</div>
                </div>
                
                <div class="stat-card">
                    <h3>Agendamentos Hoje</h3>
                    <div class="stat-value"><?php echo number_format($stats['agendamentos_hoje']); ?></div>
                    <div class="stat-label"><?php echo date('d/m/Y'); ?></div>
                </div>
                
                <div class="stat-card">
                    <h3>Total de Clientes</h3>
                    <div class="stat-value"><?php echo number_format($stats['total_clientes']); ?></div>
                    <div class="stat-label">Cadastrados</div>
                </div>
                
                <div class="stat-card">
                    <h3>Receita do Mês</h3>
                    <div class="stat-value">R$ <?php echo number_format($stats['receita_mes'], 2, ',', '.'); ?></div>
                    <div class="stat-label"><?php echo date('F Y'); ?></div>
                </div>
            </div>
            
            <div class="recent-section">
                <h2>Agendamentos Recentes</h2>
                
                <?php if (empty($agendamentos_recentes)): ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        Nenhum agendamento encontrado.
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Serviço</th>
                                    <th>Barbeiro</th>
                                    <th>Data/Hora</th>
                                    <th>Status</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($agendamentos_recentes as $agendamento): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($agendamento['cliente_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($agendamento['servico_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($agendamento['barbeiro_nome']); ?></td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($agendamento['data_agendamento'])); ?><br>
                                            <small><?php echo date('H:i', strtotime($agendamento['hora_agendamento'])); ?></small>
                                        </td>
                                        <td>
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
                                        </td>
                                        <td>R$ <?php echo number_format($agendamento['valor'], 2, ',', '.'); ?></td>
                                        <td>
                                            <a href="agendamentos.php?edit=<?php echo $agendamento['id']; ?>" 
                                               class="btn btn-sm">Editar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="agendamentos.php" class="btn">Ver Todos os Agendamentos</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>