<?php
require_once '../include/auth.php';

$auth = new Auth();
$auth->requireAdmin();

// Conectar ao banco
$conn = getDBConnection();

// Estatísticas gerais
$stats = [];

// Total de clientes
$stmt = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo_usuario = 'cliente' AND ativo = 1");
$stats['total_clientes'] = $stmt->fetch()['total'];

// Agendamentos hoje
$stmt = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE data_agendamento = CURDATE()");
$stats['agendamentos_hoje'] = $stmt->fetch()['total'];

// Agendamentos este mês
$stmt = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE MONTH(data_agendamento) = MONTH(CURDATE()) AND YEAR(data_agendamento) = YEAR(CURDATE())");
$stats['agendamentos_mes'] = $stmt->fetch()['total'];

// Receita do mês
$stmt = $conn->query("
    SELECT COALESCE(SUM(s.preco), 0) as receita 
    FROM agendamentos a 
    JOIN servicos s ON a.servico_id = s.id 
    WHERE MONTH(a.data_agendamento) = MONTH(CURDATE()) 
    AND YEAR(a.data_agendamento) = YEAR(CURDATE())
    AND a.status IN ('confirmado', 'realizado')
");
$stats['receita_mes'] = $stmt->fetch()['receita'];

// Agendamentos pendentes
$stmt = $conn->query("SELECT COUNT(*) as total FROM agendamentos WHERE status = 'agendado'");
$stats['agendamentos_pendentes'] = $stmt->fetch()['total'];

// Agendamentos de hoje
$stmt = $conn->query("
    SELECT a.*, u.nome as cliente_nome, s.nome as servico_nome, s.preco, s.duracao
    FROM agendamentos a
    JOIN usuarios u ON a.usuario_id = u.id
    JOIN servicos s ON a.servico_id = s.id
    WHERE a.data_agendamento = CURDATE()
    ORDER BY a.hora_agendamento ASC
");
$agendamentos_hoje = $stmt->fetchAll();

// Últimos clientes cadastrados
$stmt = $conn->query("
    SELECT nome, email, data_cadastro 
    FROM usuarios 
    WHERE tipo_usuario = 'cliente' 
    ORDER BY data_cadastro DESC 
    LIMIT 5
");
$ultimos_clientes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - BarberShop Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Header/Navbar -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-cut text-2xl text-indigo-600"></i>
                    <span class="text-2xl font-bold text-gray-800">BarberShop Pro</span>
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold ml-2">ADMIN</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-home mr-1"></i>Início
                    </a>
                    <span class="text-gray-600">Admin Panel</span>
                    <a href="../models/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="hero-gradient text-white rounded-xl p-8 mb-8">
            <h1 class="text-3xl font-bold mb-4 flex items-center">
                <i class="fas fa-shield-alt mr-3"></i>
                Painel Administrativo
            </h1>
            <p class="text-lg opacity-90">
                Gerencie sua barbearia de forma eficiente e mantenha seus clientes satisfeitos.
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total Clientes</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stats['total_clientes'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Hoje</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stats['agendamentos_hoje'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Este Mês</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stats['agendamentos_mes'] ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Receita Mês</p>
                        <p class="text-xl font-bold text-gray-800">R$ <?= number_format($stats['receita_mes'], 0, ',', '.') ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Pendentes</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $stats['agendamentos_pendentes'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Gerenciar Agendamentos -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Agendamentos</h3>
                    <p class="text-gray-600 text-sm mb-4">Gerenciar todos os agendamentos</p>
                    <button onclick="window.location.href='admin_agendamentos.php'" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
                        Gerenciar
                    </button>
                </div>
            </div>

            <!-- Gerenciar Clientes -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Clientes</h3>
                    <p class="text-gray-600 text-sm mb-4">Visualizar e gerenciar clientes</p>
                    <button onclick="window.location.href='admin_clientes.php'" 
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm">
                        Visualizar
                    </button>
                </div>
            </div>

            <!-- Gerenciar Serviços -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-cut text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Serviços</h3>
                    <p class="text-gray-600 text-sm mb-4">Adicionar e editar serviços</p>
                    <button onclick="window.location.href='admin_servicos.php'" 
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition text-sm">
                        Configurar
                    </button>
                </div>
            </div>

            <!-- Relatórios -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-purple-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Relatórios</h3>
                    <p class="text-gray-600 text-sm mb-4">Análises e estatísticas</p>
                    <button onclick="window.location.href='admin_relatorios.php'" 
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm">
                        Ver Relatórios
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Agendamentos de Hoje -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-calendar-day mr-2 text-indigo-600"></i>
                        Agendamentos de Hoje
                    </h2>
                </div>
                <div class="p-6">
                    <?php if (empty($agendamentos_hoje)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Nenhum agendamento para hoje</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <?php foreach ($agendamentos_hoje as $agendamento): ?>
                                <div class="border-l-4 <?= 
                                    $agendamento['status'] === 'realizado' ? 'border-green-500 bg-green-50' : 
                                    ($agendamento['status'] === 'confirmado' ? 'border-blue-500 bg-blue-50' : 
                                    ($agendamento['status'] === 'cancelado' ? 'border-red-500 bg-red-50' : 'border-yellow-500 bg-yellow-50'))
                                ?> p-4 rounded-r-lg">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($agendamento['cliente_nome']) ?></h3>
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($agendamento['servico_nome']) ?></p>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                <?= date('H:i', strtotime($agendamento['hora_agendamento'])) ?> 
                                                (<?= $agendamento['duracao'] ?>min)
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-bold text-gray-800">R$ <?= number_format($agendamento['preco'], 2, ',', '.') ?></span>
                                            <div class="mt-1">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?= 
                                                    $agendamento['status'] === 'realizado' ? 'bg-green-200 text-green-800' : 
                                                    ($agendamento['status'] === 'confirmado' ? 'bg-blue-200 text-blue-800' : 
                                                    ($agendamento['status'] === 'cancelado' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800'))
                                                ?>">
                                                    <?= ucfirst($agendamento['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4 text-center">
                        <a href="admin_agendamentos.php" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">
                            Ver todos os agendamentos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Últimos Clientes -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-plus mr-2 text-green-600"></i>
                        Clientes Recentes
                    </h2>
                </div>
                <div class="p-6">
                    <?php if (empty($ultimos_clientes)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">Nenhum cliente cadastrado ainda</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($ultimos_clientes as $cliente): ?>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="bg-indigo-100 w-10 h-10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($cliente['nome']) ?></h3>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($cliente['email']) ?></p>
                                        <p class="text-xs text-gray-500">
                                            Cadastrado em <?= date('d/m/Y', strtotime($cliente['data_cadastro'])) ?>
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Novo</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mt-4 text-center">
                        <a href="admin_clientes.php" class="text-green-600 hover:text-green-700 font-semibold text-sm">
                            Ver todos os clientes <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl p-6">
            <h2 class="text-xl font-bold mb-4">Ações Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="confirmarTodosAgendamentos()" 
                        class="bg-white/20 hover:bg-white/30 p-4 rounded-lg transition">
                    <i class="fas fa-check-double text-2xl mb-2"></i>
                    <p class="font-semibold">Confirmar Agendamentos de Hoje</p>
                </button>
                
                <button onclick="window.location.href='admin_servicos.php?action=add'" 
                        class="bg-white/20 hover:bg-white/30 p-4 rounded-lg transition">
                    <i class="fas fa-plus-circle text-2xl mb-2"></i>
                    <p class="font-semibold">Adicionar Novo Serviço</p>
                </button>
                
                <button onclick="window.location.href='admin_relatorios.php'" 
                        class="bg-white/20 hover:bg-white/30 p-4 rounded-lg transition">
                    <i class="fas fa-download text-2xl mb-2"></i>
                    <p class="font-semibold">Gerar Relatório</p>
                </button>
            </div>
        </div>
    </div>

    <script>
        function confirmarTodosAgendamentos() {
            if (confirm('Deseja confirmar todos os agendamentos de hoje?')) {
                // Aqui você implementaria a lógica para confirmar todos os agendamentos
                alert('Funcionalidade em desenvolvimento!');
            }
        }
    </script>
</body>
</html>