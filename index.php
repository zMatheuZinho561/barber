<?php
// Só inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'include/auth.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$userName = '';
$userType = '';

if ($isLoggedIn) {
    $userName = $_SESSION['usuario_nome'] ?? '';
    $userType = $_SESSION['usuario_tipo'] ?? '';
}


$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$userName = '';
$userType = '';

if ($isLoggedIn) {
    $userName = $_SESSION['usuario_nome'] ?? '';
    $userType = $_SESSION['usuario_tipo'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberShop Pro - Sua Barbearia de Confiança</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .dropdown {
            display: none;
        }
        .dropdown.show {
            display: block;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100">
    <!-- Header -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-cut text-2xl text-indigo-600"></i>
                    <span class="text-2xl font-bold text-gray-800">BarberShop Pro</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#servicos" class="text-gray-600 hover:text-indigo-600 transition">Serviços</a>
                    <a href="#sobre" class="text-gray-600 hover:text-indigo-600 transition">Sobre</a>
                    <a href="#contato" class="text-gray-600 hover:text-indigo-600 transition">Contato</a>
                    
                    <?php if ($isLoggedIn): ?>
                        <!-- Menu do usuário logado -->
                        <div class="relative">
                            <button onclick="toggleDropdown()" class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600 transition p-2 rounded-lg hover:bg-gray-100">
                                <div class="bg-indigo-100 w-8 h-8 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-indigo-600 text-sm"></i>
                                </div>
                                <span class="font-medium"><?= htmlspecialchars(explode(' ', $userName)[0]) ?></span>
                                <?php if ($userType === 'admin'): ?>
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">ADMIN</span>
                                <?php endif; ?>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="userDropdown" class="dropdown absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                                <div class="p-4 border-b border-gray-200">
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($userName) ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?></p>
                                </div>
                                <div class="py-2">
                                    <?php if ($userType === 'admin'): ?>
                                        <a href="./admin/admin_dashboard.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-tachometer-alt mr-3 text-red-500"></i>
                                            <span>Painel Admin</span>
                                        </a>
                                        <a href="admin_agendamentos.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-calendar-check mr-3 text-indigo-500"></i>
                                            <span>Gerenciar Agendamentos</span>
                                        </a>
                                        <a href="admin_clientes.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-users mr-3 text-green-500"></i>
                                            <span>Gerenciar Clientes</span>
                                        </a>
                                        <a href="admin_servicos.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-cut mr-3 text-yellow-500"></i>
                                            <span>Gerenciar Serviços</span>
                                        </a>
                                        <div class="border-t border-gray-200 mt-2 pt-2">
                                    <?php else: ?>
                                        <a href="client_dashboard.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-tachometer-alt mr-3 text-indigo-500"></i>
                                            <span>Minha Conta</span>
                                        </a>
                                        <a href="agendar.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-calendar-plus mr-3 text-green-500"></i>
                                            <span>Agendar Serviço</span>
                                        </a>
                                        <a href="meus_agendamentos.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-list-alt mr-3 text-blue-500"></i>
                                            <span>Meus Agendamentos</span>
                                        </a>
                                        <div class="border-t border-gray-200 mt-2 pt-2">
                                    <?php endif; ?>
                                        <a href="perfil.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 transition">
                                            <i class="fas fa-user-cog mr-3 text-purple-500"></i>
                                            <span>Editar Perfil</span>
                                        </a>
                                        <a href="./models/logout.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition">
                                            <i class="fas fa-sign-out-alt mr-3"></i>
                                            <span>Sair</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Botões para usuários não logados -->
                        <a href="./views/login.php" class="text-gray-600 hover:text-indigo-600 transition">Entrar</a>
                        <a href="./views/register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Cadastrar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-20">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="animate-float mb-8">
                <i class="fas fa-cut text-6xl text-white/80"></i>
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                Seu Estilo,<br>
                <span class="text-yellow-300">Nossa Arte</span>
            </h1>
            <p class="text-xl md:text-2xl mb-10 text-white/90 max-w-2xl mx-auto">
                Transforme seu visual com nossos profissionais especializados. 
                Agende seu horário e tenha uma experiência única.
            </p>
            
            <!-- Botões principais -->
            <?php if (!$isLoggedIn): ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <button onclick="window.location.href='register.php'" 
                            class="glass-effect hover:bg-white/20 text-white font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>
                        Criar Conta
                    </button>
                    <button onclick="window.location.href='login.php'" 
                            class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Entrar
                    </button>
                </div>
            <?php else: ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <?php if ($userType === 'admin'): ?>
                        <button onclick="window.location.href='admin_dashboard.php'" 
                                class="glass-effect hover:bg-white/20 text-white font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Painel Admin
                        </button>
                        <button onclick="window.location.href='admin_agendamentos.php'" 
                                class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Gerenciar Agendamentos
                        </button>
                    <?php else: ?>
                        <button onclick="window.location.href='client_dashboard.php'" 
                                class="glass-effect hover:bg-white/20 text-white font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Minha Conta
                        </button>
                        <button onclick="window.location.href='./views/agendar.php'" 
                                class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-4 px-8 rounded-full text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Agendar Serviço
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="glass-effect rounded-xl p-6">
                    <div class="text-3xl font-bold text-yellow-300">500+</div>
                    <div class="text-white/80">Clientes Satisfeitos</div>
                </div>
                <div class="glass-effect rounded-xl p-6">
                    <div class="text-3xl font-bold text-yellow-300">5</div>
                    <div class="text-white/80">Anos de Experiência</div>
                </div>
                <div class="glass-effect rounded-xl p-6">
                    <div class="text-3xl font-bold text-yellow-300">15</div>
                    <div class="text-white/80">Serviços Disponíveis</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Serviços Section -->
    <section id="servicos" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Nossos Serviços</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Oferecemos uma variedade de serviços para cuidar do seu visual
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cut text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Corte de Cabelo</h3>
                    <p class="text-gray-600 mb-4">Cortes modernos e clássicos para todos os estilos</p>
                    <span class="text-2xl font-bold text-indigo-600">R$ 25,00</span>
                </div>

                <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-tie text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Barba Completa</h3>
                    <p class="text-gray-600 mb-4">Aparar e modelar barba com técnicas profissionais</p>
                    <span class="text-2xl font-bold text-indigo-600">R$ 20,00</span>
                </div>

                <div class="bg-gray-50 rounded-xl p-8 text-center hover:shadow-xl transition-shadow">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-spa text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Tratamentos</h3>
                    <p class="text-gray-600 mb-4">Hidratação e cuidados especiais para seu cabelo</p>
                    <span class="text-2xl font-bold text-indigo-600">R$ 50,00</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre Section -->
    <section id="sobre" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">Sobre Nós</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        Com mais de 5 anos de experiência, nossa barbearia se destaca pela 
                        qualidade dos serviços e atendimento personalizado. Nossos profissionais 
                        são especializados nas mais diversas técnicas de corte e cuidados masculinos.
                    </p>
                    <div class="flex items-center space-x-4">
                        <div class="bg-indigo-600 w-12 h-12 rounded-full flex items-center justify-center">
                            <i class="fas fa-award text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Qualidade Garantida</h3>
                            <p class="text-gray-600">Satisfação em cada atendimento</p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-indigo-600 w-64 h-64 rounded-full mx-auto flex items-center justify-center">
                        <i class="fas fa-cut text-6xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-cut mr-2"></i>
                        BarberShop Pro
                    </h3>
                    <p class="text-gray-400">
                        Sua barbearia de confiança para cuidar do seu estilo.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contato</h4>
                    <div class="space-y-2 text-gray-400">
                        <p><i class="fas fa-phone mr-2"></i> (11) 99999-9999</p>
                        <p><i class="fas fa-envelope mr-2"></i> contato@barbershoppro.com</p>
                        <p><i class="fas fa-map-marker-alt mr-2"></i> Rua das Tesouras, 123</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Horário de Funcionamento</h4>
                    <div class="space-y-2 text-gray-400">
                        <p>Segunda - Sexta: 8h às 18h</p>
                        <p>Sábado: 8h às 17h</p>
                        <p>Domingo: Fechado</p>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 BarberShop Pro. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Dropdown toggle
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('[onclick="toggleDropdown()"]');
            
            if (!button && dropdown) {
                dropdown.classList.remove('show');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>