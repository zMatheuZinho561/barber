<?php
// Só inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ajustar caminho conforme sua estrutura
$authPath = file_exists('includes/auth.php') ? 'includes/auth.php' : 'include/auth.php';
require_once $authPath;

$auth = new Auth();
$auth->requireAdmin();

$conn = getDBConnection();
$selectedDate = $_GET['data'] ?? date('Y-m-d');

// Buscar barbeiros
$stmt = $conn->query("SELECT * FROM barbeiros WHERE ativo = 1 ORDER BY nome");
$barbeiros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda dos Barbeiros - BarberShop Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }
        .status-agendado { background-color: #fef3c7; color: #92400e; }
        .status-confirmado { background-color: #dbeafe; color: #1e40af; }
        .status-realizado { background-color: #dcfce7; color: #166534; }
        .status-cancelado { background-color: #fee2e2; color: #dc2626; }
        
        .agenda-slot {
            min-height: 60px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .agenda-slot:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }
        .agenda-occupied {
            background-color: #f3f4f6;
            border-color: #6b7280;
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
                    <a href="admin_dashboard.php" class="text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="hero-gradient text-white rounded-xl p-8 mb-8">
            <h1 class="text-3xl font-bold mb-4 flex items-center">
                <i class="fas fa-calendar-alt mr-3"></i>
                Agenda dos Barbeiros
            </h1>
            <p class="text-lg opacity-90">
                Visualize e gerencie os agendamentos de todos os barbeiros por dia.
            </p>
        </div>

        <!-- Controles -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <!-- Seletor de Data -->
                <div class="flex items-center space-x-4">
                    <label class="text-gray-700 font-semibold">
                        <i class="fas fa-calendar mr-2"></i>Data:
                    </label>
                    <input type="date" 
                           id="date-selector" 
                           value="<?= $selectedDate ?>"
                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           onchange="changeDate()">
                    
                    <div class="flex space-x-2">
                        <button onclick="changeDay(-1)" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="goToToday()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Hoje
                        </button>
                        <button onclick="changeDay(1)" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Controles de Atualização -->
                <div class="flex items-center space-x-4">
                    <button onclick="refreshAgenda()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-sync-alt mr-2"></i>Atualizar
                    </button>
                    <div class="flex items-center">
                        <input type="checkbox" id="auto-refresh" checked class="mr-2">
                        <label for="auto-refresh" class="text-sm text-gray-600">Auto-atualizar (30s)</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo do Dia -->
        <div id="resumo-container" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Será preenchido via JavaScript -->
        </div>

        <!-- Agenda dos Barbeiros -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Agenda do Dia - <span id="current-date-display"><?= date('d/m/Y', strtotime($selectedDate)) ?></span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Cabeçalho -->
                    <div class="grid grid-cols-<?= count($barbeiros) + 1 ?> bg-gray-50 border-b border-gray-200">
                        <div class="p-4 font-semibold text-gray-700 border-r border-gray-200">
                            <i class="fas fa-clock mr-2"></i>Horário
                        </div>
                        <?php foreach ($barbeiros as $barbeiro): ?>
                            <div class="p-4 font-semibold text-gray-700 border-r border-gray-200 text-center">
                                <i class="fas fa-user-tie mr-2"></i>
                                <?= htmlspecialchars($barbeiro['nome']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Grade de Horários -->
                    <div id="agenda-grid">
                        <!-- Será preenchido via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Legenda -->
        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Legenda:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded status-agendado mr-2"></div>
                    <span class="text-sm text-gray-600">Agendado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded status-confirmado mr-2"></div>
                    <span class="text-sm text-gray-600">Confirmado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded status-realizado mr-2"></div>
                    <span class="text-sm text-gray-600">Realizado</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded status-cancelado mr-2"></div>
                    <span class="text-sm text-gray-600">Cancelado</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes do Agendamento -->
    <div id="agendamentoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Detalhes do Agendamento</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modal-content">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const barbeiros = <?= json_encode($barbeiros) ?>;
        let currentDate = '<?= $selectedDate ?>';
        let autoRefreshInterval;

        document.addEventListener('DOMContentLoaded', function() {
            loadAgenda();
            setupAutoRefresh();
        });

        function setupAutoRefresh() {
            const checkbox = document.getElementById('auto-refresh');
            
            if (checkbox.checked) {
                autoRefreshInterval = setInterval(loadAgenda, 30000); // 30 segundos
            }

            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    autoRefreshInterval = setInterval(loadAgenda, 30000);
                } else {
                    clearInterval(autoRefreshInterval);
                }
            });
        }

        function changeDate() {
            currentDate = document.getElementById('date-selector').value;
            updateDateDisplay();
            loadAgenda();
        }

        function changeDay(offset) {
            const date = new Date(currentDate);
            date.setDate(date.getDate() + offset);
            currentDate = date.toISOString().split('T')[0];
            document.getElementById('date-selector').value = currentDate;
            updateDateDisplay();
            loadAgenda();
        }

        function goToToday() {
            currentDate = new Date().toISOString().split('T')[0];
            document.getElementById('date-selector').value = currentDate;
            updateDateDisplay();
            loadAgenda();
        }

        function updateDateDisplay() {
            const date = new Date(currentDate + 'T00:00:00');
            const formattedDate = date.toLocaleDateString('pt-BR');
            document.getElementById('current-date-display').textContent = formattedDate;
        }

        async function loadAgenda() {
            try {
                // Carregar resumo do dia
                await loadResumo();
                
                // Carregar agenda de cada barbeiro
                await loadAgendaBarbeiros();
                
            } catch (error) {
                console.error('Erro ao carregar agenda:', error);
                showError('Erro ao carregar agenda: ' + error.message);
            }
        }

        async function loadResumo() {
            try {
                const response = await fetch(`api/agendamento.php?action=get_resumo_dia&data=${currentDate}`);
                const resumo = await response.json();

                if (response.ok) {
                    renderResumo(resumo);
                } else {
                    throw new Error(resumo.error || 'Erro ao carregar resumo');
                }
            } catch (error) {
                console.error('Erro no resumo:', error);
            }
        }

        function renderResumo(resumo) {
            const container = document.getElementById('resumo-container');
            let totalAgendamentos = 0;
            let totalReceita = 0;

            resumo.forEach(barbeiro => {
                totalAgendamentos += parseInt(barbeiro.total_agendamentos) || 0;
                totalReceita += parseFloat(barbeiro.receita_potencial) || 0;
            });

            container.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-blue-600 text-sm font-medium">Total do Dia</p>
                            <p class="text-2xl font-bold text-gray-800">${totalAgendamentos} agendamentos</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-green-600 text-sm font-medium">Receita Potencial</p>
                            <p class="text-2xl font-bold text-gray-800">R$ ${totalReceita.toFixed(2).replace('.', ',')}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-purple-600 text-sm font-medium">Barbeiros Ativos</p>
                            <p class="text-2xl font-bold text-gray-800">${barbeiros.length}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-yellow-600 text-sm font-medium">Horário</p>
                            <p class="text-lg font-bold text-gray-800">8h às 18h</p>
                        </div>
                    </div>
                </div>
            `;
        }

        async function loadAgendaBarbeiros() {
            const agendaGrid = document.getElementById('agenda-grid');
            const horarios = generateTimeSlots();
            
            // Buscar agendamentos de todos os barbeiros
            const agendamentosPromises = barbeiros.map(barbeiro => 
                fetch(`api/agendamento.php?action=get_agenda_barbeiro&barbeiro_id=${barbeiro.id}&data=${currentDate}`)
                    .then(response => response.json())
                    .then(data => ({ barbeiro_id: barbeiro.id, agendamentos: data }))
            );

            try {
                const resultados = await Promise.all(agendamentosPromises);
                const agendamentosPorBarbeiro = {};
                
                resultados.forEach(resultado => {
                    agendamentosPorBarbeiro[resultado.barbeiro_id] = resultado.agendamentos;
                });

                renderAgendaGrid(horarios, agendamentosPorBarbeiro);
                
            } catch (error) {
                console.error('Erro ao carregar agendamentos:', error);
                showError('Erro ao carregar agendamentos dos barbeiros');
            }
        }

        function generateTimeSlots() {
            const slots = [];
            const start = 8; // 8:00
            const end = 18;  // 18:00

            for (let hour = start; hour < end; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    slots.push(timeString);
                }
            }
            return slots;
        }

        function renderAgendaGrid(horarios, agendamentosPorBarbeiro) {
            const grid = document.getElementById('agenda-grid');
            grid.innerHTML = '';

            horarios.forEach(horario => {
                const row = document.createElement('div');
                row.className = `grid grid-cols-${barbeiros.length + 1} border-b border-gray-200`;

                // Coluna do horário
                const timeCell = document.createElement('div');
                timeCell.className = 'p-3 font-medium text-gray-600 border-r border-gray-200 bg-gray-50 text-center';
                timeCell.textContent = horario;
                row.appendChild(timeCell);

                // Colunas dos barbeiros
                barbeiros.forEach(barbeiro => {
                    const cell = document.createElement('div');
                    cell.className = 'agenda-slot p-2 border-r border-gray-200 relative';
                    
                    // Verificar se há agendamento neste horário
                    const agendamentos = agendamentosPorBarbeiro[barbeiro.id] || [];
                    const agendamento = agendamentos.find(ag => {
                        const agHora = ag.hora_agendamento.substring(0, 5); // HH:MM
                        return agHora === horario;
                    });

                    if (agendamento) {
                        cell.classList.add('agenda-occupied');
                        cell.innerHTML = `
                            <div class="status-${agendamento.status} rounded p-2 text-xs cursor-pointer h-full flex flex-col justify-center"
                                 onclick="showAgendamentoDetails(${JSON.stringify(agendamento).replace(/"/g, '&quot;')})">
                                <div class="font-semibold truncate">${agendamento.cliente_nome}</div>
                                <div class="truncate">${agendamento.servico_nome}</div>
                                <div class="text-xs opacity-75">${agendamento.servico_duracao}min - R$ ${parseFloat(agendamento.servico_preco).toFixed(2)}</div>
                            </div>
                        `;
                    } else {
                        cell.innerHTML = '<div class="h-full flex items-center justify-center text-gray-300"><i class="fas fa-plus"></i></div>';
                        cell.onclick = () => newAgendamento(barbeiro.id, currentDate, horario);
                    }

                    row.appendChild(cell);
                });

                grid.appendChild(row);
            });
        }

        function showAgendamentoDetails(agendamento) {
            const modal = document.getElementById('agendamentoModal');
            const content = document.getElementById('modal-content');
            
            const statusColors = {
                'agendado': 'bg-yellow-100 text-yellow-800',
                'confirmado': 'bg-blue-100 text-blue-800', 
                'realizado': 'bg-green-100 text-green-800',
                'cancelado': 'bg-red-100 text-red-800'
            };

            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cliente</label>
                            <p class="text-lg font-semibold">${agendamento.cliente_nome}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Telefone</label>
                            <p class="text-lg">${agendamento.cliente_telefone || 'Não informado'}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Serviço</label>
                            <p class="text-lg font-semibold">${agendamento.servico_nome}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Barbeiro</label>
                            <p class="text-lg">${agendamento.barbeiro_nome}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Data</label>
                            <p class="text-lg">${new Date(agendamento.data_agendamento + 'T00:00:00').toLocaleDateString('pt-BR')}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Horário</label>
                            <p class="text-lg">${agendamento.hora_agendamento.substring(0, 5)}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duração</label>
                            <p class="text-lg">${agendamento.servico_duracao} min</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium ${statusColors[agendamento.status] || 'bg-gray-100 text-gray-800'}">
                                ${agendamento.status.charAt(0).toUpperCase() + agendamento.status.slice(1)}
                            </span>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Valor</label>
                            <p class="text-xl font-bold text-green-600">R$ ${parseFloat(agendamento.servico_preco).toFixed(2).replace('.', ',')}</p>
                        </div>
                    </div>
                    
                    ${agendamento.observacoes ? `
                        <div>
                            <label class="text-sm font-medium text-gray-500">Observações</label>
                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">${agendamento.observacoes}</p>
                        </div>
                    ` : ''}
                    
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        ${agendamento.status === 'agendado' ? `
                            <button onclick="updateStatus(${agendamento.agendamento_id}, 'confirmado')" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-check mr-1"></i> Confirmar
                            </button>
                        ` : ''}
                        
                        ${agendamento.status === 'confirmado' ? `
                            <button onclick="updateStatus(${agendamento.agendamento_id}, 'realizado')" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check-circle mr-1"></i> Finalizar
                            </button>
                        ` : ''}
                        
                        ${agendamento.status !== 'cancelado' && agendamento.status !== 'realizado' ? `
                            <button onclick="updateStatus(${agendamento.agendamento_id}, 'cancelado')" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function newAgendamento(barbeiroId, data, horario) {
            // Implementar criação de novo agendamento pelo admin (opcional)
            alert(`Criar agendamento para barbeiro ${barbeiroId} em ${data} às ${horario}`);
        }

        async function updateStatus(agendamentoId, novoStatus) {
            try {
                // Implementar endpoint para atualizar status
                const response = await fetch('api/agendamento.php?action=update_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        agendamento_id: agendamentoId,
                        status: novoStatus
                    })
                });

                const result = await response.json();
                
                if (response.ok && result.success) {
                    closeModal();
                    loadAgenda();
                    showSuccess(`Status atualizado para ${novoStatus}`);
                } else {
                    throw new Error(result.error || 'Erro ao atualizar status');
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Erro ao atualizar status: ' + error.message);
            }
        }

        function closeModal() {
            document.getElementById('agendamentoModal').classList.add('hidden');
        }

        function refreshAgenda() {
            loadAgenda();
            showSuccess('Agenda atualizada!');
        }

        function showError(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            toast.innerHTML = `<i class="fas fa-exclamation-circle mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        function showSuccess(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Cleanup ao sair da página
        window.addEventListener('beforeunload', function() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        });
    </script>
</body>
</html>