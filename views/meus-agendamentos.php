<?php
// meus-agendamentos.php
session_start();

// Verificar se está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: index.php");
    exit();
}

$usuarioNome = $_SESSION['usuario_nome'] ?? '';
$usuarioEmail = $_SESSION['usuario_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - BarberShop Elite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f172a',
                        secondary: '#f59e0b',
                        accent: '#dc2626',
                        gold: '#fbbf24',
                        dark: '#1e293b'
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(245, 158, 11, 0.4);
        }
        
        .agendamento-card {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .agendamento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .status-agendado { @apply bg-blue-100 text-blue-800 border-blue-200; }
        .status-confirmado { @apply bg-green-100 text-green-800 border-green-200; }
        .status-cancelado { @apply bg-red-100 text-red-800 border-red-200; }
        .status-finalizado { @apply bg-gray-100 text-gray-800 border-gray-200; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-secondary to-gold rounded-xl flex items-center justify-center">
                            <i class="fas fa-cut text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-primary">BarberShop Elite</h1>
                        </div>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700 font-medium"><?= htmlspecialchars($usuarioNome) ?></span>
                    <button onclick="logout()" class="text-red-600 hover:text-red-700 font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Sair
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary to-dark rounded-3xl p-8 text-white mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">Meus Agendamentos</h1>
                        <p class="text-gray-300 text-lg">Acompanhe seus horários marcados</p>
                    </div>
                    <button onclick="window.location.href='index.php#servicos'" 
                            class="btn-primary px-6 py-3 rounded-full font-semibold text-white mt-4 md:mt-0">
                        <i class="fas fa-plus mr-2"></i>
                        Novo Agendamento
                    </button>
                </div>
            </div>
            
            <!-- Elementos decorativos -->
            <div class="absolute top-4 right-20 w-8 h-8 border border-secondary/30 rounded-full"></div>
            <div class="absolute bottom-4 left-20 w-6 h-6 bg-gradient-to-br from-secondary/20 to-transparent rounded-full"></div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl p-6 mb-8 shadow-sm">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Filtrar Agendamentos</h2>
                <div class="flex flex-wrap gap-3">
                    <button onclick="filtrarPorStatus('todos')" class="filter-btn filter-active px-4 py-2 rounded-full font-medium transition duration-300" data-status="todos">
                        Todos
                    </button>
                    <button onclick="filtrarPorStatus('agendado')" class="filter-btn px-4 py-2 rounded-full font-medium transition duration-300" data-status="agendado">
                        Agendados
                    </button>
                    <button onclick="filtrarPorStatus('confirmado')" class="filter-btn px-4 py-2 rounded-full font-medium transition duration-300" data-status="confirmado">
                        Confirmados
                    </button>
                    <button onclick="filtrarPorStatus('finalizado')" class="filter-btn px-4 py-2 rounded-full font-medium transition duration-300" data-status="finalizado">
                        Finalizados
                    </button>
                    <button onclick="filtrarPorStatus('cancelado')" class="filter-btn px-4 py-2 rounded-full font-medium transition duration-300" data-status="cancelado">
                        Cancelados
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de Agendamentos -->
        <div id="agendamentosContainer">
            <!-- Loading -->
            <div id="loadingAgendamentos" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-secondary mb-4"></i>
                <p class="text-gray-600 text-lg">Carregando seus agendamentos...</p>
            </div>
            
            <!-- Container dos agendamentos será preenchido dinamicamente -->
            <div id="listaAgendamentos" class="hidden space-y-6"></div>
            
            <!-- Nenhum agendamento -->
            <div id="nenhumAgendamento" class="hidden text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Nenhum agendamento encontrado</h3>
                <p class="text-gray-600 mb-6">Você ainda não tem agendamentos. Que tal marcar um horário?</p>
                <button onclick="window.location.href='index.php#servicos'" 
                        class="btn-primary px-8 py-3 rounded-full font-semibold text-white">
                    <i class="fas fa-calendar-plus mr-2"></i>
                    Fazer Agendamento
                </button>
            </div>
        </div>
    </main>

    <!-- Modal de Confirmação de Cancelamento -->
    <div id="modalCancelar" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl max-w-md w-full mx-4 shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Cancelar Agendamento</h3>
                <p class="text-gray-600">Tem certeza que deseja cancelar este agendamento?</p>
                <p class="text-sm text-red-600 mt-2">Esta ação não pode ser desfeita.</p>
            </div>
            
            <div class="flex space-x-4">
                <button onclick="fecharModalCancelar()" 
                        class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-2xl font-bold hover:border-gray-400 transition duration-300">
                    Não, manter
                </button>
                <button onclick="confirmarCancelamento()" 
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition duration-300">
                    Sim, cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- Toast de Notificação -->
    <div id="toast" class="fixed top-8 right-8 z-50 transform translate-x-full transition-transform duration-500">
        <div class="bg-white rounded-2xl shadow-2xl p-6 flex items-center space-x-4 min-w-80 border">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-green-500">
                <i class="fas fa-check text-white"></i>
            </div>
            <div>
                <h4 class="text-gray-800 font-semibold">Sucesso!</h4>
                <p id="toastMessage" class="text-gray-600 text-sm">Operação realizada com sucesso!</p>
            </div>
            <button onclick="fecharToast()" class="text-gray-400 hover:text-gray-600 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        let agendamentos = [];
        let agendamentoParaCancelar = null;
        let filtroAtual = 'todos';

        // Carregar agendamentos
        async function carregarAgendamentos() {
            try {
                const response = await fetch('api/agendamentos.php?acao=listar');
                const data = await response.json();
                
                document.getElementById('loadingAgendamentos').classList.add('hidden');
                
                if (data.success) {
                    agendamentos = data.data;
                    renderizarAgendamentos();
                } else {
                    mostrarNenhumAgendamento();
                }
            } catch (error) {
                document.getElementById('loadingAgendamentos').classList.add('hidden');
                console.error('Erro:', error);
                mostrarToast('Erro ao carregar agendamentos', 'error');
            }
        }

        // Renderizar agendamentos
        function renderizarAgendamentos(filtro = 'todos') {
            const container = document.getElementById('listaAgendamentos');
            const nenhumContainer = document.getElementById('nenhumAgendamento');
            
            let agendamentosFiltrados = agendamentos;
            
            if (filtro !== 'todos') {
                agendamentosFiltrados = agendamentos.filter(ag => ag.status === filtro);
            }
            
            if (agendamentosFiltrados.length === 0) {
                container.classList.add('hidden');
                nenhumContainer.classList.remove('hidden');
                return;
            }
            
            nenhumContainer.classList.add('hidden');
            container.classList.remove('hidden');
            
            container.innerHTML = agendamentosFiltrados.map(agendamento => {
                const data = new Date(agendamento.data_agendamento + 'T00:00:00');
                const dataFormatada = data.toLocaleDateString('pt-BR', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                const statusClass = `status-${agendamento.status}`;
                const statusIcon = getStatusIcon(agendamento.status);
                const statusText = getStatusText(agendamento.status);
                
                const podeSerCancelado = podeSerCanceladoCheck(agendamento);
                
                return `
                    <div class="agendamento-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <!-- Informações principais -->
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-primary to-dark rounded-2xl flex items-center justify-center">
                                        <i class="fas fa-cut text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-800">${agendamento.servico_nome}</h3>
                                        <p class="text-gray-600">com ${agendamento.barbeiro_nome}</p>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                            <span><i class="fas fa-clock mr-1"></i>${agendamento.servico_duracao} minutos</span>
                                            <span><i class="fas fa-dollar-sign mr-1"></i>R$ ${parseFloat(agendamento.valor_total).toFixed(2).replace('.', ',')}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Data e horário -->
                                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                    <div class="flex items-center gap-6">
                                        <div class="flex items-center text-gray-700">
                                            <i class="fas fa-calendar text-secondary mr-2"></i>
                                            <span class="font-medium">${dataFormatada}</span>
                                        </div>
                                        <div class="flex items-center text-gray-700">
                                            <i class="fas fa-clock text-secondary mr-2"></i>
                                            <span class="font-medium">${agendamento.hora_agendamento}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                ${agendamento.observacoes ? `
                                    <div class="bg-blue-50 rounded-xl p-4 mb-4">
                                        <h4 class="font-medium text-blue-800 mb-2">
                                            <i class="fas fa-comment mr-2"></i>Observações
                                        </h4>
                                        <p class="text-blue-700 text-sm">${agendamento.observacoes}</p>
                                    </div>
                                ` : ''}
                            </div>
                            
                            <!-- Status e ações -->
                            <div class="flex flex-col items-end gap-4">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border ${statusClass}">
                                    <i class="${statusIcon} mr-2"></i>
                                    ${statusText}
                                </span>
                                
                                <div class="flex gap-2">
                                    ${podeSerCancelado ? `
                                        <button onclick="abrirModalCancelar(${agendamento.id})" 
                                                class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition duration-300 font-medium">
                                            <i class="fas fa-times mr-2"></i>Cancelar
                                        </button>
                                    ` : ''}
                                    
                                    <button onclick="verDetalhes(${agendamento.id})" 
                                            class="px-4 py-2 text-secondary hover:bg-secondary/10 rounded-lg transition duration-300 font-medium">
                                        <i class="fas fa-eye mr-2"></i>Detalhes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Verificar se pode ser cancelado
        function podeSerCanceladoCheck(agendamento) {
            if (agendamento.status === 'cancelado' || agendamento.status === 'finalizado') {
                return false;
            }
            
            const agora = new Date();
            const dataHoraAgendamento = new Date(agendamento.data_agendamento + 'T' + agendamento.hora_agendamento);
            const duasHoras = 2 * 60 * 60 * 1000; // 2 horas em milissegundos
            
            return (dataHoraAgendamento.getTime() - agora.getTime()) > duasHoras;
        }

        // Obter ícone do status
        function getStatusIcon(status) {
            const icons = {
                'agendado': 'fas fa-clock',
                'confirmado': 'fas fa-check-circle',
                'cancelado': 'fas fa-times-circle',
                'finalizado': 'fas fa-check-double'
            };
            return icons[status] || 'fas fa-question-circle';
        }

        // Obter texto do status
        function getStatusText(status) {
            const texts = {
                'agendado': 'Agendado',
                'confirmado': 'Confirmado',
                'cancelado': 'Cancelado',
                'finalizado': 'Finalizado'
            };
            return texts[status] || 'Desconhecido';
        }

        // Mostrar quando não há agendamentos
        function mostrarNenhumAgendamento() {
            document.getElementById('listaAgendamentos').classList.add('hidden');
            document.getElementById('nenhumAgendamento').classList.remove('hidden');
        }

        // Filtrar por status
        function filtrarPorStatus(status) {
            filtroAtual = status;
            
            // Atualizar botões de filtro
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('filter-active', 'bg-secondary', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            });
            
            const btnAtivo = document.querySelector(`[data-status="${status}"]`);
            btnAtivo.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            btnAtivo.classList.add('filter-active', 'bg-secondary', 'text-white');
            
            renderizarAgendamentos(status);
        }

        // Abrir modal de cancelamento
        function abrirModalCancelar(agendamentoId) {
            agendamentoParaCancelar = agendamentoId;
            document.getElementById('modalCancelar').classList.remove('hidden');
            document.getElementById('modalCancelar').classList.add('flex');
        }

        // Fechar modal de cancelamento
        function fecharModalCancelar() {
            agendamentoParaCancelar = null;
            document.getElementById('modalCancelar').classList.add('hidden');
            document.getElementById('modalCancelar').classList.remove('flex');
        }

        // Confirmar cancelamento
        async function confirmarCancelamento() {
            if (!agendamentoParaCancelar) return;
            
            try {
                const formData = new FormData();
                formData.append('acao', 'cancelar');
                formData.append('id', agendamentoParaCancelar);
                
                const response = await fetch('api/agendamentos.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Agendamento cancelado com sucesso!', 'success');
                    fecharModalCancelar();
                    
                    // Recarregar agendamentos
                    setTimeout(() => {
                        carregarAgendamentos();
                    }, 1000);
                } else {
                    mostrarToast(data.message, 'error');
                }
            } catch (error) {
                mostrarToast('Erro ao cancelar agendamento', 'error');
                console.error('Erro:', error);
            }
        }

        // Ver detalhes (placeholder)
        function verDetalhes(agendamentoId) {
            const agendamento = agendamentos.find(ag => ag.id == agendamentoId);
            if (agendamento) {
                const detalhes = `
                    Agendamento #${agendamento.id}
                    Serviço: ${agendamento.servico_nome}
                    Barbeiro: ${agendamento.barbeiro_nome}
                    Data: ${new Date(agendamento.data_agendamento + 'T00:00:00').toLocaleDateString('pt-BR')}
                    Horário: ${agendamento.hora_agendamento}
                    Valor: R$ ${parseFloat(agendamento.valor_total).toFixed(2).replace('.', ',')}
                    Status: ${getStatusText(agendamento.status)}
                `;
                alert(detalhes);
            }
        }

        // Toast de notificação
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const icon = toast.querySelector('i');
            const iconContainer = toast.querySelector('.w-12');
            
            toastMessage.textContent = mensagem;
            
            if (tipo === 'error') {
                iconContainer.className = iconContainer.className.replace(/bg-\w+-500/, 'bg-red-500');
                icon.className = 'fas fa-times text-white';
            } else {
                iconContainer.className = iconContainer.className.replace(/bg-\w+-500/, 'bg-green-500');
                icon.className = 'fas fa-check text-white';
            }
            
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            setTimeout(() => {
                fecharToast();
            }, 5000);
        }

        function fecharToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('translate-x-full');
            toast.classList.remove('translate-x-0');
        }

        // Logout
        async function logout() {
            try {
                const response = await fetch('api/auth.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        acao: 'logout'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'index.php';
                }
            } catch (error) {
                console.error('Erro:', error);
                window.location.href = 'index.php';
            }
        }

        // Inicializar página
        document.addEventListener('DOMContentLoaded', function() {
            carregarAgendamentos();
            
            // Configurar filtros iniciais
            document.querySelector('[data-status="todos"]').classList.add('filter-active', 'bg-secondary', 'text-white');
            
            // Fechar modal ao clicar fora
            document.getElementById('modalCancelar').addEventListener('click', function(e) {
                if (e.target === this) {
                    fecharModalCancelar();
                }
            });
        });

        // Estilos CSS adicionais
        const style = document.createElement('style');
        style.textContent = `
            .filter-active {
                background-color: #f59e0b !important;
                color: white !important;
            }
            
            .filter-btn {
                background-color: #f3f4f6;
                color: #374151;
            }
            
            .filter-btn:hover:not(.filter-active) {
                background-color: #e5e7eb;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>