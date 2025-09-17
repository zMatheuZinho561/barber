<?php
require_once '../include/auth.php';

$auth = new Auth();
$auth->requireLogin();

// Redirecionar admin para dashboard admin
if ($auth->isAdmin()) {
    header("Location: admin_dashboard.php");
    exit();
}

// Conectar ao banco para buscar dados do usuário
$conn = getDBConnection();

// Buscar dados do usuário logado
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Buscar agendamentos do usuário
$stmt = $conn->prepare("
    SELECT a.*, s.nome as servico_nome, s.preco, s.duracao 
    FROM agendamentos a 
    JOIN servicos s ON a.servico_id = s.id 
    WHERE a.usuario_id = ? 
    ORDER BY a.data_agendamento DESC, a.hora_agendamento DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['usuario_id']]);
$agendamentos = $stmt->fetchAll();

// Próximo agendamento
$stmt = $conn->prepare("
    SELECT a.*, s.nome as servico_nome, s.preco, s.duracao 
    FROM agendamentos a 
    JOIN servicos s ON a.servico_id = s.id 
    WHERE a.usuario_id = ? AND a.data_agendamento >= CURDATE() AND a.status IN ('agendado', 'confirmado')
    ORDER BY a.data_agendamento ASC, a.hora_agendamento ASC
    LIMIT 1
");
$stmt->execute([$_SESSION['usuario_id']]);
$proximo_agendamento = $stmt->fetch();

// Estatísticas do cliente
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$total_agendamentos = $stmt->fetch()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE usuario_id = ? AND status = 'realizado'");
$stmt->execute([$_SESSION['usuario_id']]);
$agendamentos_realizados = $stmt->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - BarberShop Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-home mr-1"></i>Início
                    </a>
                    <span class="text-gray-600">Olá, <?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?>!</span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="hero-gradient text-white rounded-xl p-8 mb-8">
            <h1 class="text-3xl font-bold mb-4">
                Olá, <?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?>! 👋
            </h1>
            <p class="text-lg opacity-90">
                Gerencie seus agendamentos e mantenha seu estilo sempre em dia.
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Total de Agendamentos</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_agendamentos ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Serviços Realizados</p>
                        <p class="text-2xl font-bold text-gray-800"><?= $agendamentos_realizados ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Cliente desde</p>
                        <p class="text-lg font-bold text-gray-800"><?= date('m/Y', strtotime($usuario['data_cadastro'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximo Agendamento -->
        <?php if ($proximo_agendamento): ?>
            <div class="bg-gradient-to-r from-green-400 to-blue-500 text-white rounded-xl p-6 mb-8">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-clock mr-2"></i>Seu Próximo Agendamento
                </h2>
                <div class="bg-white/20 rounded-lg p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($proximo_agendamento['servico_nome']) ?></h3>
                            <p class="opacity-90">
                                <i class="fas fa-calendar mr-1"></i>
                                <?= date('d/m/Y', strtotime($proximo_agendamento['data_agendamento'])) ?> às 
                                <?= date('H:i', strtotime($proximo_agendamento['hora_agendamento'])) ?>
                            </p>
                            <p class="opacity-90">
                                <i class="fas fa-clock mr-1"></i>
                                Duração: <?= $proximo_agendamento['duracao'] ?> minutos
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span class="text-2xl font-bold">R$ <?= number_format($proximo_agendamento['preco'], 2, ',', '.') ?></span>
                            <p class="text-sm opacity-75">Status: <?= ucfirst($proximo_agendamento['status']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Agendar Serviço -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-calendar-plus text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Novo Agendamento</h3>
                    <p class="text-gray-600 mb-4">Marque seu próximo corte ou serviço</p>
                    <button onclick="window.location.href='agendar.php'" 
                            class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition transform hover:scale-105">
                        <i class="fas fa-plus mr-2"></i>Agendar Agora
                    </button>
                </div>
            </div>

            <!-- Meus Agendamentos -->
            <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="text-center">
                    <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-list-alt text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Meus Agendamentos</h3>
                    <p class="text-gray-600 mb-4">Visualize e gerencie seus horários</p>
                    <button onclick="window.location.href='meus_agendamentos.php'" 
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition transform hover:scale-105">
                        <i class="fas fa-eye mr-2"></i>Ver Histórico
                    </button>
                </div>
            </div>
        </div>

        <!-- Agendamentos Recentes -->
        <?php if (!empty($agendamentos)): ?>
            <div class="bg-white rounded-xl shadow-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-history mr-2 text-gray-600"></i>
                        Agendamentos Recentes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($agendamentos as $agendamento): ?>
                            <div class="border-l-4 <?= 
                                $agendamento['status'] === 'realizado' ? 'border-green-500 bg-green-50' : 
                                ($agendamento['status'] === 'confirmado' ? 'border-blue-500 bg-blue-50' : 
                                ($agendamento['status'] === 'cancelado' ? 'border-red-500 bg-red-50' : 'border-yellow-500 bg-yellow-50'))
                            ?> p-4 rounded-r-lg hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($agendamento['servico_nome']) ?></h3>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-gray-600 flex items-center">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                <?= date('d/m/Y', strtotime($agendamento['data_agendamento'])) ?> às 
                                                <?= date('H:i', strtotime($agendamento['hora_agendamento'])) ?>
                                            </p>
                                            <p class="text-gray-600 flex items-center">
                                                <i class="fas fa-clock mr-2 text-gray-400"></i>
                                                <?= $agendamento['duracao'] ?> minutos
                                            </p>
                                            <p class="text-gray-600 flex items-center">
                                                <i class="fas fa-dollar-sign mr-2 text-gray-400"></i>
                                                R$ <?= number_format($agendamento['preco'], 2, ',', '.') ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?= 
                                            $agendamento['status'] === 'realizado' ? 'bg-green-100 text-green-800' : 
                                            ($agendamento['status'] === 'confirmado' ? 'bg-blue-100 text-blue-800' : 
                                            ($agendamento['status'] === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'))
                                        ?>">
                                            <?= ucfirst($agendamento['status']) ?>
                                        </span>
                                        <p class="text-xs text-gray-500 mt-2">
                                            ID: #<?= $agendamento['id'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="meus_agendamentos.php" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-semibold transition">
                            Ver todos os agendamentos 
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Primeira visita -->
            <div class="bg-indigo-50 rounded-xl border-2 border-dashed border-indigo-200 p-8">
                <div class="text-center">
                    <i class="fas fa-calendar-day text-6xl text-indigo-400 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Ainda não há agendamentos</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Que tal marcar seu primeiro horário? Nossos profissionais estão prontos para cuidar do seu estilo!
                    </p>
                    <button onclick="window.location.href='agendar.php'" 
                            class="bg-indigo-600 text-white px-8 py-4 rounded-lg hover:bg-indigo-700 transition transform hover:scale-105 text-lg font-semibold">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Fazer Primeiro Agendamento
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>