<?php
// Só inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../include/auth.php';

$auth = new Auth();
$auth->requireLogin();

// Redirecionar admin
if ($auth->isAdmin()) {
    header("Location: admin_dashboard.php");
    exit();
}

$conn = getDBConnection();

// Buscar dados do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Serviço - BarberShop Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .horario-btn {
            transition: all 0.3s ease;
        }
        .horario-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .horario-btn.selected {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.05);
        }
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .loading-overlay {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
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
                    <a href="client_dashboard.php" class="text-gray-600 hover:text-indigo-600 transition">
                        <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                    </a>
                    <span class="text-gray-600">Olá, <?= htmlspecialchars(explode(' ', $usuario['nome'])[0]) ?>!</span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 loading-overlay z-50 flex items-center justify-center hidden">
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto mb-4"></div>
            <p class="text-gray-700 font-semibold">Carregando horários...</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="hero-gradient text-white rounded-xl p-8 mb-8">
            <h1 class="text-3xl font-bold mb-4 flex items-center">
                <i class="fas fa-calendar-plus mr-3"></i>
                Agendar Serviço
            </h1>
            <p class="text-lg opacity-90">
                Escolha o serviço, data e horário para seu agendamento.
            </p>
        </div>

        <!-- Formulário de Agendamento -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form id="agendamentoForm" class="space-y-8">
                <!-- Seleção de Serviço -->
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-4">
                        <i class="fas fa-cut mr-2 text-indigo-600"></i>
                        Escolha o Serviço
                    </label>
                    <div id="servicos-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Serviços serão carregados via JavaScript -->
                    </div>
                </div>

                <!-- Seleção de Data -->
                <div class="hidden" id="data-section">
                    <label class="block text-gray-700 text-lg font-semibold mb-4">
                        <i class="fas fa-calendar mr-2 text-indigo-600"></i>
                        Escolha a Data
                    </label>
                    <div id="dates-container" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                        <!-- Próximos 14 dias úteis -->
                    </div>
                </div>

                <!-- Seleção de Horário -->
                <div class="hidden" id="horario-section">
                    <label class="block text-gray-700 text-lg font-semibold mb-4">
                        <i class="fas fa-clock mr-2 text-indigo-600"></i>
                        Horários Disponíveis
                        <span id="data-selecionada" class="text-indigo-600 font-normal"></span>
                    </label>
                    <div id="horarios-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        <!-- Horários serão carregados via JavaScript -->
                    </div>
                    <div id="no-horarios" class="hidden text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 text-lg">Não há horários disponíveis para esta data</p>
                        <p class="text-gray-500">Escolha outra data</p>
                    </div>
                </div>

                <!-- Observações -->
                <div class="hidden" id="observacoes-section">
                    <label class="block text-gray-700 text-lg font-semibold mb-4">
                        <i class="fas fa-comment mr-2 text-indigo-600"></i>
                        Observações (Opcional)
                    </label>
                    <textarea id="observacoes" name="observacoes" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                              rows="3" 
                              placeholder="Alguma observação especial para seu atendimento?"></textarea>
                </div>

                <!-- Resumo do Agendamento -->
                <div class="hidden bg-indigo-50 rounded-xl p-6 border border-indigo-200" id="resumo-section">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clipboard-list mr-2 text-indigo-600"></i>
                        Resumo do Agendamento
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Serviço:</span>
                            <span id="resumo-servico" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Data:</span>
                            <span id="resumo-data" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Horário:</span>
                            <span id="resumo-horario" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Duração:</span>
                            <span id="resumo-duracao" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center border-t pt-3">
                            <span class="text-lg font-bold text-gray-800">Total:</span>
                            <span id="resumo-preco" class="text-2xl font-bold text-indigo-600"></span>
                        </div>
                    </div>
                </div>

                <!-- Botão de Confirmação -->
                <div class="hidden text-center" id="confirmar-section">
                    <button type="submit" 
                            class="bg-indigo-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-check mr-2"></i>
                        Confirmar Agendamento
                    </button>
                </div>
            </form>
        </div>

        <!-- Status da Conexão -->
        <div class="mt-4 text-center">
            <span id="connection-status" class="text-sm text-green-600">
                <i class="fas fa-wifi mr-1"></i>
                Conectado - Atualizando horários automaticamente
            </span>
        </div>
    </div>

    <!-- Modal de Sucesso -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
            <div class="bg-green-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-check text-2xl text-green-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Agendamento Confirmado!</h3>
            <p class="text-gray-600 mb-6">Seu agendamento foi criado com sucesso. Você receberá uma confirmação em breve.</p>
            <button onclick="closeSuccessModal()" 
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                OK
            </button>
        </div>
    </div>

    <script>
        let selectedService = null;
        let selectedDate = null;
        let selectedTime = null;
        let availableServices = [];

        // Auto-refresh a cada 30 segundos
        let refreshInterval = null;

        document.addEventListener('DOMContentLoaded', function() {
            loadServices();
            generateDateOptions();
            
            // Configurar auto-refresh
            refreshInterval = setInterval(function() {
                if (selectedDate && selectedService) {
                    loadAvailableTimes(selectedDate, selectedService.id, false);
                    updateConnectionStatus('Horários atualizados automaticamente');
                }
            }, 30000); // 30 segundos
        });

        function updateConnectionStatus(message) {
            const status = document.getElementById('connection-status');
            status.innerHTML = `<i class="fas fa-sync-alt mr-1"></i>${message}`;
            setTimeout(() => {
                status.innerHTML = '<i class="fas fa-wifi mr-1"></i>Conectado - Atualizando horários automaticamente';
            }, 3000);
        }

        async function loadServices() {
            try {
                const response = await fetch('../api/agendamento.php?action=get_servicos');
                const services = await response.json();
                
                if (response.ok) {
                    availableServices = services;
                    renderServices(services);
                } else {
                    throw new Error(services.error || 'Erro ao carregar serviços');
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Erro ao carregar serviços: ' + error.message);
            }
        }

        function renderServices(services) {
            const container = document.getElementById('servicos-container');
            container.innerHTML = '';

            services.forEach(service => {
                const serviceCard = document.createElement('div');
                serviceCard.className = 'service-card bg-gray-50 border-2 border-gray-200 rounded-xl p-6 cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-300';
                serviceCard.onclick = () => selectService(service);
                
                serviceCard.innerHTML = `
                    <div class="text-center">
                        <div class="bg-indigo-100 w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center">
                            <i class="fas fa-cut text-indigo-600"></i>
                        </div>
                        <h3 class="font-bold text-gray-800 mb-2">${service.nome}</h3>
                        <p class="text-sm text-gray-600 mb-3">${service.descricao || 'Serviço profissional'}</p>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-indigo-600">R$ ${parseFloat(service.preco).toFixed(2).replace('.', ',')}</p>
                            <p class="text-sm text-gray-500">${service.duracao} minutos</p>
                        </div>
                    </div>
                `;
                
                container.appendChild(serviceCard);
            });
        }

        function selectService(service) {
            selectedService = service;
            
            // Atualizar visual de seleção
            document.querySelectorAll('.service-card').forEach(card => {
                card.classList.remove('border-indigo-500', 'bg-indigo-100');
                card.classList.add('border-gray-200', 'bg-gray-50');
            });
            
            event.currentTarget.classList.remove('border-gray-200', 'bg-gray-50');
            event.currentTarget.classList.add('border-indigo-500', 'bg-indigo-100');
            
            // Mostrar seção de data
            document.getElementById('data-section').classList.remove('hidden');
            
            // Reset seleções posteriores
            selectedDate = null;
            selectedTime = null;
            document.getElementById('horario-section').classList.add('hidden');
            document.getElementById('observacoes-section').classList.add('hidden');
            document.getElementById('resumo-section').classList.add('hidden');
            document.getElementById('confirmar-section').classList.add('hidden');
        }

        function generateDateOptions() {
            const container = document.getElementById('dates-container');
            const today = new Date();
            let daysAdded = 0;
            let dayOffset = 0;

            while (daysAdded < 14) {
                const currentDate = new Date(today);
                currentDate.setDate(today.getDate() + dayOffset);
                
                // Pular domingos (0 = domingo)
                if (currentDate.getDay() !== 0) {
                    const dateStr = currentDate.toISOString().split('T')[0];
                    const dayName = currentDate.toLocaleDateString('pt-BR', { weekday: 'short' });
                    const dayNum = currentDate.getDate();
                    const monthName = currentDate.toLocaleDateString('pt-BR', { month: 'short' });
                    
                    const dateButton = document.createElement('button');
                    dateButton.type = 'button';
                    dateButton.className = 'date-btn bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-300 text-center';
                    dateButton.onclick = () => selectDate(dateStr, dateButton);
                    
                    dateButton.innerHTML = `
                        <div class="text-sm text-gray-500 capitalize">${dayName}</div>
                        <div class="text-lg font-bold text-gray-800">${dayNum}</div>
                        <div class="text-sm text-gray-500 capitalize">${monthName}</div>
                    `;
                    
                    container.appendChild(dateButton);
                    daysAdded++;
                }
                dayOffset++;
            }
        }

        function selectDate(dateStr, buttonElement) {
            selectedDate = dateStr;
            
            // Atualizar visual de seleção
            document.querySelectorAll('.date-btn').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'bg-indigo-100');
                btn.classList.add('border-gray-200', 'bg-white');
            });
            
            buttonElement.classList.remove('border-gray-200', 'bg-white');
            buttonElement.classList.add('border-indigo-500', 'bg-indigo-100');
            
            // Atualizar data selecionada no cabeçalho
            const dateObj = new Date(dateStr + 'T00:00:00');
            const formattedDate = dateObj.toLocaleDateString('pt-BR', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            document.getElementById('data-selecionada').textContent = ` - ${formattedDate}`;
            
            // Carregar horários disponíveis
            document.getElementById('horario-section').classList.remove('hidden');
            loadAvailableTimes(dateStr, selectedService.id);
            
            // Reset seleções posteriores
            selectedTime = null;
            document.getElementById('observacoes-section').classList.add('hidden');
            document.getElementById('resumo-section').classList.add('hidden');
            document.getElementById('confirmar-section').classList.add('hidden');
        }

        async function loadAvailableTimes(date, serviceId, showLoading = true) {
            const container = document.getElementById('horarios-container');
            const noHorarios = document.getElementById('no-horarios');
            
            if (showLoading) {
                document.getElementById('loadingOverlay').classList.remove('hidden');
            }

            try {
                const response = await fetch(`../api/agendamento.php?action=get_horarios_disponiveis&data=${date}&servico_id=${serviceId}`);
                const horarios = await response.json();

                if (response.ok) {
                    container.innerHTML = '';
                    
                    if (horarios.length === 0) {
                        noHorarios.classList.remove('hidden');
                        container.classList.add('hidden');
                    } else {
                        noHorarios.classList.add('hidden');
                        container.classList.remove('hidden');
                        
                        horarios.forEach(horario => {
                            const timeButton = document.createElement('button');
                            timeButton.type = 'button';
                            timeButton.className = 'horario-btn bg-white border-2 border-gray-200 rounded-lg py-3 px-4 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-300 font-semibold';
                            timeButton.onclick = () => selectTime(horario, timeButton);
                            timeButton.textContent = horario.horario_formatado;
                            
                            container.appendChild(timeButton);
                        });
                    }
                } else {
                    throw new Error(horarios.error || 'Erro ao carregar horários');
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Erro ao carregar horários: ' + error.message);
            } finally {
                if (showLoading) {
                    document.getElementById('loadingOverlay').classList.add('hidden');
                }
            }
        }

        function selectTime(horario, buttonElement) {
            selectedTime = horario;
            
            // Atualizar visual de seleção
            document.querySelectorAll('.horario-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            buttonElement.classList.add('selected');
            
            // Mostrar próximas seções
            document.getElementById('observacoes-section').classList.remove('hidden');
            document.getElementById('resumo-section').classList.remove('hidden');
            document.getElementById('confirmar-section').classList.remove('hidden');
            
            // Atualizar resumo
            updateSummary();
            
            // Scroll para o resumo
            document.getElementById('resumo-section').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }

        function updateSummary() {
            if (!selectedService || !selectedDate || !selectedTime) return;

            const dateObj = new Date(selectedDate + 'T00:00:00');
            const formattedDate = dateObj.toLocaleDateString('pt-BR', { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'long' 
            });

            document.getElementById('resumo-servico').textContent = selectedService.nome;
            document.getElementById('resumo-data').textContent = formattedDate;
            document.getElementById('resumo-horario').textContent = selectedTime.horario_formatado;
            document.getElementById('resumo-duracao').textContent = selectedService.duracao + ' minutos';
            document.getElementById('resumo-preco').textContent = 'R$ ' + parseFloat(selectedService.preco).toFixed(2).replace('.', ',');
        }

        // Submissão do formulário
        document.getElementById('agendamentoForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!selectedService || !selectedDate || !selectedTime) {
                showError('Por favor, complete todas as seleções');
                return;
            }

            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Confirmando...';
            submitBtn.disabled = true;

            const observacoes = document.getElementById('observacoes').value;

            try {
                const response = await fetch('../api/agendamento.php?action=criar_agendamento', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        servico_id: selectedService.id,
                        data_agendamento: selectedDate,
                        hora_agendamento: selectedTime.horario,
                        observacoes: observacoes
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showSuccessModal();
                    
                    // Parar auto-refresh após sucesso
                    if (refreshInterval) {
                        clearInterval(refreshInterval);
                    }
                } else {
                    throw new Error(result.error || 'Erro ao criar agendamento');
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Erro ao confirmar agendamento: ' + error.message);
                
                // Se o horário não está mais disponível, recarregar horários
                if (error.message.includes('não está mais disponível')) {
                    setTimeout(() => {
                        loadAvailableTimes(selectedDate, selectedService.id);
                    }, 2000);
                }
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
            window.location.href = 'client_dashboard.php';
        }

        function showError(message) {
            // Criar toast de erro
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 max-w-sm';
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 flex-shrink-0"></i>
                    <span class="flex-1">${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 flex-shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Mostrar toast
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Remover automaticamente após 5 segundos
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Limpar interval quando sair da página
        window.addEventListener('beforeunload', function() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });

        // Detectar quando a página fica visível novamente (mobile)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden && selectedDate && selectedService) {
                loadAvailableTimes(selectedDate, selectedService.id, false);
                updateConnectionStatus('Horários sincronizados');
            }
        });

        // Adicionar suporte a gestos touch para mobile
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', function(e) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            
            const deltaX = touchEndX - touchStartX;
            const deltaY = touchEndY - touchStartY;
            
            // Swipe para baixo para atualizar horários (pull to refresh)
            if (deltaY > 50 && Math.abs(deltaX) < 100 && selectedDate && selectedService && window.scrollY === 0) {
                loadAvailableTimes(selectedDate, selectedService.id);
                updateConnectionStatus('Horários atualizados manualmente');
            }
        }, { passive: true });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC para fechar modal
            if (e.key === 'Escape') {
                const modal = document.getElementById('successModal');
                if (!modal.classList.contains('hidden')) {
                    closeSuccessModal();
                }
            }
            
            // Enter para confirmar quando tudo estiver selecionado
            if (e.key === 'Enter' && selectedService && selectedDate && selectedTime) {
                const submitBtn = document.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.click();
                }
            }
        });

        // Função para verificar conectividade
        function checkConnectivity() {
            if (navigator.onLine) {
                document.getElementById('connection-status').innerHTML = 
                    '<i class="fas fa-wifi mr-1"></i>Conectado - Atualizando horários automaticamente';
                document.getElementById('connection-status').className = 'text-sm text-green-600';
            } else {
                document.getElementById('connection-status').innerHTML = 
                    '<i class="fas fa-wifi-slash mr-1"></i>Sem conexão - Verifique sua internet';
                document.getElementById('connection-status').className = 'text-sm text-red-600';
            }
        }

        // Monitorar status da conexão
        window.addEventListener('online', checkConnectivity);
        window.addEventListener('offline', checkConnectivity);

        // Verificar conectividade inicial
        checkConnectivity();
    </script>
</body>
</html>