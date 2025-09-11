<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Barbearia Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a202c',
                        secondary: '#2d3748',
                        accent: '#ed8936',
                        dark: '#0f0f0f'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-in': 'slideIn 0.5s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
        <div class="flex items-center justify-center h-16 bg-gray-800 border-b border-gray-700">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold">✂</span>
                </div>
                <span class="text-white font-bold text-lg">Admin Panel</span>
            </div>
        </div>
        
        <nav class="mt-8">
            <div class="px-4 space-y-2">
                <a href="#dashboard" onclick="showSection('dashboard')" class="nav-item active flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v3h8V5a2 2 0 00-2-2H10a2 2 0 00-2 2z"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="#agendamentos" onclick="showSection('agendamentos')" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                    </svg>
                    Agendamentos
                    <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full" id="agendamentos-count">0</span>
                </a>
                
                <a href="#barbeiros" onclick="showSection('barbeiros')" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Barbeiros
                </a>
                
                <a href="#servicos" onclick="showSection('servicos')" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Serviços
                </a>
                
                <a href="#clientes" onclick="showSection('clientes')" class="nav-item flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    Clientes
                </a>
            </div>
            
            <div class="px-4 mt-8 pt-8 border-t border-gray-700">
                <a href="../index.php" class="flex items-center px-4 py-3 text-gray-400 hover:text-gray-300 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ver Site
                </a>
                
                <button onclick="logout()" class="flex items-center w-full px-4 py-3 text-gray-400 hover:text-red-400 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sair
                </button>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-600 hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h1 class="ml-4 text-2xl font-bold text-gray-900" id="page-title">Dashboard</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5V3h5v14z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm" id="user-avatar">A</span>
                        </div>
                        <span class="text-gray-700 font-medium" id="user-name">Admin</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-6">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="animate-fade-in">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 bg-opacity-10 rounded-full">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Agendamentos</p>
                                <p class="text-2xl font-bold text-gray-900" id="total-agendamentos">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in" style="animation-delay: 0.1s">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Hoje</p>
                                <p class="text-2xl font-bold text-gray-900" id="agendamentos-hoje">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in" style="animation-delay: 0.2s">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 bg-opacity-10 rounded-full">
                                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Clientes</p>
                                <p class="text-2xl font-bold text-gray-900" id="total-clientes">0</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in" style="animation-delay: 0.3s">
                        <div class="flex items-center">
                            <div class="p-3 bg-orange-500 bg-opacity-10 rounded-full">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Receita Mensal</p>
                                <p class="text-2xl font-bold text-gray-900" id="receita-mes">R$ 0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in" style="animation-delay: 0.4s">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Agendamentos por Mês</h3>
                        <canvas id="monthlyChart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 animate-slide-in" style="animation-delay: 0.5s">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status dos Agendamentos</h3>
                        <canvas id="statusChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 animate-slide-in" style="animation-delay: 0.6s">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Agendamentos Recentes</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="recent-appointments">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barbeiro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="appointments-tbody">
                                <!-- Data loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Other sections will be loaded dynamically -->
            <div id="agendamentos-section" class="hidden animate-fade-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gerenciar Agendamentos</h3>
                    <p class="text-gray-600">Seção de agendamentos em desenvolvimento...</p>
                </div>
            </div>

            <div id="barbeiros-section" class="hidden animate-fade-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gerenciar Barbeiros</h3>
                    <p class="text-gray-600">Seção de barbeiros em desenvolvimento...</p>
                </div>
            </div>

            <div id="servicos-section" class="hidden animate-fade-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gerenciar Serviços</h3>
                    <p class="text-gray-600">Seção de serviços em desenvolvimento...</p>
                </div>
            </div>

            <div id="clientes-section" class="hidden animate-fade-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Gerenciar Clientes</h3>
                    <p class="text-gray-600">Seção de clientes em desenvolvimento...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Loading Overlay -->
    <div id="loading" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
            <span class="text-gray-700">Carregando dados...</span>
        </div>
    </div>

    <script>
        let dashboardData = {};
        let charts = {};

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
            loadDashboardData();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Sidebar toggle
            document.getElementById('sidebar-toggle').addEventListener('click', toggleSidebar);
            
            // Navigation items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Show section
        function showSection(section) {
            // Hide all sections
            document.querySelectorAll('[id$="-section"]').forEach(sec => {
                sec.classList.add('hidden');
            });
            
            // Show selected section
            document.getElementById(section + '-section').classList.remove('hidden');
            
            // Update page title
            const titles = {
                dashboard: 'Dashboard',
                agendamentos: 'Agendamentos',
                barbeiros: 'Barbeiros', 
                servicos: 'Serviços',
                clientes: 'Clientes'
            };
            document.getElementById('page-title').textContent = titles[section] || 'Dashboard';
        }

        // Load dashboard data
        async function loadDashboardData() {
            try {
                const response = await fetch('../api/admin_dashboard.php');
                const result = await response.json();
                
                if (result.success) {
                    dashboardData = result.data;
                    updateDashboardUI();
                    initializeCharts();
                } else {
                    throw new Error(result.message || 'Erro ao carregar dados');
                }
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
                showAlert('Erro ao carregar dados do dashboard', 'error');
            } finally {
                hideLoading();
            }
        }

        // Update dashboard UI
        function updateDashboardUI() {
            // Update stats
            document.getElementById('total-agendamentos').textContent = dashboardData.stats.total_agendamentos || 0;
            document.getElementById('agendamentos-hoje').textContent = dashboardData.stats.agendamentos_hoje || 0;
            document.getElementById('total-clientes').textContent = dashboardData.stats.total_clientes || 0;
            document.getElementById('receita-mes').textContent = formatCurrency(dashboardData.stats.receita_mes || 0);
            
            // Update navigation badge
            document.getElementById('agendamentos-count').textContent = dashboardData.stats.agendamentos_hoje || 0;
            
            // Update user info
            if (dashboardData.user) {
                document.getElementById('user-name').textContent = dashboardData.user.nome;
                document.getElementById('user-avatar').textContent = dashboardData.user.nome.charAt(0).toUpperCase();
            }
            
            // Update recent appointments
            updateRecentAppointments();
        }

        // Update recent appointments table
        function updateRecentAppointments() {
            const tbody = document.getElementById('appointments-tbody');
            
            if (!dashboardData.recent_appointments || dashboardData.recent_appointments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Nenhum agendamento encontrado
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = dashboardData.recent_appointments.map(appointment => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${appointment.cliente_nome}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${appointment.servico_nome}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${appointment.barbeiro_nome}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${formatDate(appointment.data_agendamento)}</div>
                        <div class="text-sm text-gray-500">${appointment.hora_agendamento}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(appointment.status)}">
                            ${getStatusText(appointment.status)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ${formatCurrency(appointment.valor)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editAppointment(${appointment.id})" class="text-orange-600 hover:text-orange-900 mr-4">Editar</button>
                        <button onclick="deleteAppointment(${appointment.id})" class="text-red-600 hover:text-red-900">Excluir</button>
                    </td>
                </tr>
            `).join('');
        }

        // Initialize charts
        function initializeCharts() {
            initMonthlyChart();
            initStatusChart();
        }

        // Initialize monthly appointments chart
        function initMonthlyChart() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyData = dashboardData.monthly_data || [];
            
            charts.monthly = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => d.month),
                    datasets: [{
                        label: 'Agendamentos',
                        data: monthlyData.map(d => d.count),
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        x: {
                            grid: {
                                color: '#f3f4f6'
                            }
                        }
                    }
                }
            });
        }

        // Initialize status chart
        function initStatusChart() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            const statusData = dashboardData.status_data || [];
            
            charts.status = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: statusData.map(d => getStatusText(d.status)),
                    datasets: [{
                        data: statusData.map(d => d.count),
                        backgroundColor: [
                            '#3b82f6', // agendado - blue
                            '#10b981', // confirmado - green  
                            '#8b5cf6', // concluido - purple
                            '#ef4444'  // cancelado - red
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Utility functions
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('pt-BR');
        }

        function getStatusColor(status) {
            const colors = {
                'agendado': 'bg-blue-100 text-blue-800',
                'confirmado': 'bg-green-100 text-green-800',
                'concluido': 'bg-purple-100 text-purple-800',
                'cancelado': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const texts = {
                'agendado': 'Agendado',
                'confirmado': 'Confirmado',
                'concluido': 'Concluído',
                'cancelado': 'Cancelado'
            };
            return texts[status] || status;
        }

        // Action functions
        function editAppointment(id) {
            showAlert('Função de editar em desenvolvimento', 'info');
        }

        function deleteAppointment(id) {
            if (confirm('Tem certeza que deseja excluir este agendamento?')) {
                showAlert('Função de excluir em desenvolvimento', 'info');
            }
        }

        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                fetch('../api/logout.php', { method: 'POST' })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            window.location.href = '../index.php';
                        }
                    })
                    .catch(error => {
                        console.error('Erro no logout:', error);
                        window.location.href = '../index.php';
                    });
            }
        }

        // Show alert
        function showAlert(message, type) {
            const alertColors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'info': 'bg-blue-500',
                'warning': 'bg-yellow-500'
            };

            const alert = document.createElement('div');
            alert.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg text-white shadow-lg transform translate-x-full transition-transform duration-300 ${alertColors[type] || alertColors.info}`;
            alert.textContent = message;
            
            document.body.appendChild(alert);
            
            setTimeout(() => {
                alert.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                alert.classList.add('translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(alert)) {
                        document.body.removeChild(alert);
                    }
                }, 300);
            }, 4000);
        }

        // Hide loading
        function hideLoading() {
            const loading = document.getElementById('loading');
            loading.style.opacity = '0';
            setTimeout(() => {
                loading.style.display = 'none';
            }, 300);
        }

        // Handle responsive behavior
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
            } else {
                document.getElementById('sidebar').classList.add('-translate-x-full');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            
            if (window.innerWidth < 1024 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) &&
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    </script>
</body>
</html>