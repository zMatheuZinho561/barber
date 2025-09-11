<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Barbearia Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">✂</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Barbearia Premium</h1>
                        <p class="text-sm text-gray-600">Meu Perfil</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        Início
                    </a>
                    <a href="agendamento.php" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Agendar
                    </a>
                    <button onclick="logout()" class="text-gray-600 hover:text-red-600 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                        Sair
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl shadow-lg overflow-hidden mb-8 animate-slide-in">
            <div class="px-8 py-12 text-white">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold" id="user-avatar">U</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-2">Bem-vindo, <span id="user-name">Usuário</span>!</h1>
                        <p class="text-orange-100">Gerencie seus agendamentos e perfil aqui</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold mb-1" id="total-appointments">0</div>
                        <div class="text-orange-100 text-sm">Total de Agendamentos</div>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold mb-1" id="next-appointment">--</div>
                        <div class="text-orange-100 text-sm">Próximo Agendamento</div>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-4 backdrop-blur-sm">
                        <div class="text-2xl font-bold mb-1" id="total-spent">R$ 0</div>
                        <div class="text-orange-100 text-sm">Total Gasto</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-all duration-300 animate-scale-in">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Novo Agendamento</h3>
                <p class="text-gray-600 text-sm mb-4">Agende um novo horário</p>
                <a href="agendamento.php" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    Agendar
                </a>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-all duration-300 animate-scale-in" style="animation-delay: 0.1s">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Ver Serviços</h3>
                <p class="text-gray-600 text-sm mb-4">Conheça nossos serviços</p>
                <a href="index.php#servicos" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    Ver Todos
                </a>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-all duration-300 animate-scale-in" style="animation-delay: 0.2s">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Contato</h3>
                <p class="text-gray-600 text-sm mb-4">Entre em contato</p>
                <a href="tel:+5511999990000" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    Ligar
                </a>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-all duration-300 animate-scale-in" style="animation-delay: 0.3s">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Meus Dados</h3>
                <p class="text-gray-600 text-sm mb-4">Editar informações</p>
                <button onclick="showEditProfile()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    Editar
                </button>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 animate-fade-in" style="animation-delay: 0.4s">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Meus Agendamentos</h2>
                <div class="flex space-x-2">
                    <button onclick="filterAppointments('all')" class="filter-btn active px-4 py-2 text-sm rounded-lg transition-colors">Todos</button>
                    <button onclick="filterAppointments('upcoming')" class="filter-btn px-4 py-2 text-sm rounded-lg transition-colors">Próximos</button>
                    <button onclick="filterAppointments('completed')" class="filter-btn px-4 py-2 text-sm rounded-lg transition-colors">Concluídos</button>
                </div>
            </div>
            
            <div id="appointments-container" class="p-6">
                <!-- Appointments loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-scale-in">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Editar Perfil</h3>
                <button onclick="hideEditProfile()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="editProfileForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                    <input type="text" id="edit-nome" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="edit-email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="edit-telefone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>
                
                <div class="flex space-x-4 pt-4">
                    <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition-colors">
                        Salvar
                    </button>
                    <button type="button" onclick="hideEditProfile()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
            <span class="text-gray-700">Carregando...</span>
        </div>
    </div>

    <script>
        let userProfile = {};
        let appointments = [];
        let currentFilter = 'all';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadUserProfile();
            loadAppointments();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('editProfileForm').addEventListener('submit', handleEditProfile);
        }

        // Load user profile
        async function loadUserProfile() {
            try {
                const response = await fetch('../api/user_profile.php');
                const result = await response.json();
                
                if (result.success) {
                    userProfile = result.user;
                    updateProfileUI();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao carregar perfil:', error);
                showAlert('Erro ao carregar perfil do usuário', 'error');
            }
        }

        // Load appointments
        async function loadAppointments() {
            try {
                const response = await fetch('../api/user_appointments.php');
                const result = await response.json();
                
                if (result.success) {
                    appointments = result.appointments;
                    updateAppointmentsUI();
                    updateStatsUI();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao carregar agendamentos:', error);
                showAlert('Erro ao carregar agendamentos', 'error');
            } finally {
                hideLoading();
            }
        }

        // Update profile UI
        function updateProfileUI() {
            document.getElementById('user-name').textContent = userProfile.nome || 'Usuário';
            document.getElementById('user-avatar').textContent = (userProfile.nome || 'U').charAt(0).toUpperCase();
            
            // Fill edit form
            document.getElementById('edit-nome').value = userProfile.nome || '';
            document.getElementById('edit-email').value = userProfile.email || '';
            document.getElementById('edit-telefone').value = userProfile.telefone || '';
        }

        // Update stats UI
        function updateStatsUI() {
            const totalAppointments = appointments.length;
            const totalSpent = appointments
                .filter(apt => apt.status === 'concluido')
                .reduce((sum, apt) => sum + parseFloat(apt.valor || 0), 0);
            
            const upcomingAppointments = appointments.filter(apt => 
                apt.status === 'agendado' || apt.status === 'confirmado'
            );
            
            const nextAppointment = upcomingAppointments
                .sort((a, b) => new Date(a.data_agendamento + ' ' + a.hora_agendamento) - 
                              new Date(b.data_agendamento + ' ' + b.hora_agendamento))[0];
            
            document.getElementById('total-appointments').textContent = totalAppointments;
            document.getElementById('total-spent').textContent = formatCurrency(totalSpent);
            
            if (nextAppointment) {
                const nextDate = new Date(nextAppointment.data_agendamento);
                document.getElementById('next-appointment').textContent = 
                    nextDate.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
            } else {
                document.getElementById('next-appointment').textContent = '--';
            }
        }

        // Update appointments UI
        function updateAppointmentsUI() {
            const container = document.getElementById('appointments-container');
            
            let filteredAppointments = appointments;
            
            if (currentFilter === 'upcoming') {
                filteredAppointments = appointments.filter(apt => 
                    apt.status === 'agendado' || apt.status === 'confirmado'
                );
            } else if (currentFilter === 'completed') {
                filteredAppointments = appointments.filter(apt => 
                    apt.status === 'concluido'
                );
            }
            
            if (filteredAppointments.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum agendamento encontrado</h3>
                        <p class="text-gray-500 mb-6">Que tal fazer seu primeiro agendamento?</p>
                        <a href="agendamento.php" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                            Agendar Agora
                        </a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="space-y-4">
                    ${filteredAppointments.map(appointment => `
                        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-all duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1">${appointment.servico_nome}</h3>
                                    <p class="text-gray-600">com ${appointment.barbeiro_nome}</p>
                                </div>
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full ${getStatusColor(appointment.status)}">
                                    ${getStatusText(appointment.status)}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M7 7h10v4l-2 2v6H9v-6l-2-2V7z"/>
                                    </svg>
                                    ${formatDate(appointment.data_agendamento)}
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    ${appointment.hora_agendamento}
                                </div>
                                
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    ${formatCurrency(appointment.valor)}
                                </div>
                                
                                <div class="flex items-center justify-end space-x-2">
                                    ${appointment.status === 'agendado' && canCancelAppointment(appointment) ? `
                                        <button onclick="cancelAppointment(${appointment.id})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Cancelar
                                        </button>
                                    ` : ''}
                                </div>
                            </div>
                            
                            ${appointment.observacoes ? `
                                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-700"><strong>Observações:</strong> ${appointment.observacoes}</p>
                                </div>
                            ` : ''}
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Filter appointments
        function filterAppointments(filter) {
            currentFilter = filter;
            
            // Update filter buttons
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            updateAppointmentsUI();
        }

        // Show edit profile modal
        function showEditProfile() {
            document.getElementById('editProfileModal').classList.remove('hidden');
            document.getElementById('editProfileModal').classList.add('flex');
        }

        // Hide edit profile modal
        function hideEditProfile() {
            document.getElementById('editProfileModal').classList.add('hidden');
            document.getElementById('editProfileModal').classList.remove('flex');
        }

        // Handle edit profile form
        async function handleEditProfile(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('nome', document.getElementById('edit-nome').value);
            formData.append('email', document.getElementById('edit-email').value);
            formData.append('telefone', document.getElementById('edit-telefone').value);
            
            try {
                const response = await fetch('../api/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Perfil atualizado com sucesso!', 'success');
                    hideEditProfile();
                    await loadUserProfile(); // Reload profile data
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao atualizar perfil:', error);
                showAlert('Erro ao atualizar perfil', 'error');
            }
        }

        // Cancel appointment
        async function cancelAppointment(appointmentId) {
            if (!confirm('Tem certeza que deseja cancelar este agendamento?')) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('appointment_id', appointmentId);
                
                const response = await fetch('../api/cancel_appointment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Agendamento cancelado com sucesso!', 'success');
                    await loadAppointments(); // Reload appointments
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Erro ao cancelar agendamento:', error);
                showAlert('Erro ao cancelar agendamento', 'error');
            }
        }

        // Check if appointment can be cancelled
        function canCancelAppointment(appointment) {
            const appointmentDateTime = new Date(appointment.data_agendamento + ' ' + appointment.hora_agendamento);
            const now = new Date();
            const hoursDiff = (appointmentDateTime - now) / (1000 * 60 * 60);
            
            return hoursDiff > 2; // Can cancel if more than 2 hours ahead
        }

        // Logout
        async function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                try {
                    const response = await fetch('../api/logout.php', { method: 'POST' });
                    const result = await response.json();
                    
                    if (result.success) {
                        window.location.href = 'index.php';
                    }
                } catch (error) {
                    console.error('Erro no logout:', error);
                    window.location.href = 'index.php';
                }
            }
        }

        // Utility functions
        function formatCurrency(value) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value || 0);
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

        // Show alert
        function showAlert(message, type) {
            const alertColors = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'info': 'bg-blue-500'
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

        // Add styles for filter buttons
        document.addEventListener('DOMContentLoaded', function() {
            const style = document.createElement('style');
            style.textContent = `
                .filter-btn {
                    background-color: #f3f4f6;
                    color: #6b7280;
                }
                .filter-btn:hover {
                    background-color: #e5e7eb;
                    color: #374151;
                }
                .filter-btn.active {
                    background-color: #f97316;
                    color: white;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>