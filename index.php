<?php
session_start();
require_once 'models/Servico.php';

// Verificar se usuário está logado
$usuarioLogado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;
$usuarioNome = $_SESSION['usuario_nome'] ?? '';
$usuarioEmail = $_SESSION['usuario_email'] ?? '';
$usuarioTipo = $_SESSION['usuario_tipo'] ?? '';

// Redirecionar admin para dashboard
if ($usuarioLogado && $usuarioTipo === 'admin') {
    header('Location: admin/admin_dashboard.php');
    exit();
}

// Buscar serviços
$servicoModel = new Servico();
$servicos = $servicoModel->listarTodos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarberShop Elite - O melhor em cortes masculinos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Definir variável global do usuário logado
        const usuarioLogado = <?= json_encode($usuarioLogado) ?>;
        const usuarioTipo = <?= json_encode($usuarioTipo) ?>;
        
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
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
            to { box-shadow: 0 0 30px rgba(245, 158, 11, 0.6); }
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(245,158,11,0.1)"/><circle cx="75" cy="25" r="1" fill="rgba(245,158,11,0.08)"/><circle cx="50" cy="50" r="1" fill="rgba(245,158,11,0.12)"/><circle cx="25" cy="75" r="1" fill="rgba(245,158,11,0.06)"/><circle cx="75" cy="75" r="1" fill="rgba(245,158,11,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .service-card {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .service-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        
        .navbar-blur {
            backdrop-filter: blur(20px);
            background: rgba(15, 23, 42, 0.9);
            border-bottom: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(245, 158, 11, 0.4);
        }

        /* Fix para o conflito de layout */
        @media (max-width: 768px) {
            .user-menu-container {
                position: relative;
                width: 100%;
            }
            
            .user-menu-buttons {
                flex-direction: column;
                gap: 0.75rem;
                width: 100%;
            }
            
            .user-menu-buttons > * {
                width: 100%;
                justify-content: center;
            }
        }

        @media (min-width: 769px) {
            .user-menu-container {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans">
    <!-- Navbar Moderna -->
    <nav class="navbar-blur fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-gold rounded-xl flex items-center justify-center animate-glow">
                            <i class="fas fa-cut text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">BarberShop</h1>
                        <span class="text-sm gradient-text font-semibold">Elite</span>
                    </div>
                </div>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-white hover:text-secondary transition duration-300 font-medium">Início</a>
                    <a href="#servicos" class="text-white hover:text-secondary transition duration-300 font-medium">Serviços</a>
                    <a href="#sobre" class="text-white hover:text-secondary transition duration-300 font-medium">Sobre</a>
                    <a href="#contato" class="text-white hover:text-secondary transition duration-300 font-medium">Contato</a>
                </div>
                
                <!-- Botões de Usuário -->
                <div class="user-menu-container">
                    <?php if ($usuarioLogado): ?>
                        <!-- Se logado -->
                        <div class="user-menu-buttons flex items-center gap-4">
                            <!-- Dropdown do usuário -->
                            <div class="relative">
                                <button id="userMenuButton" class="flex items-center space-x-3 text-white hover:text-secondary transition duration-300 bg-white/10 px-4 py-2 rounded-full glass-effect">
                                    <div class="w-8 h-8 bg-gradient-to-br from-secondary to-gold rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span class="hidden lg:block font-medium max-w-32 truncate"><?= htmlspecialchars($usuarioNome) ?></span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <!-- Dropdown Moderno -->
                                <div id="userDropdown" class="absolute right-0 mt-3 w-64 glass-effect rounded-2xl shadow-2xl py-2 hidden transform opacity-0 scale-95 transition-all duration-200">
                                    <div class="px-4 py-3 border-b border-white/10">
                                        <p class="text-white font-medium truncate"><?= htmlspecialchars($usuarioNome) ?></p>
                                        <p class="text-gray-300 text-sm truncate"><?= htmlspecialchars($usuarioEmail) ?></p>
                                    </div>
                                    <a href="#" class="flex items-center px-4 py-3 text-white hover:bg-white/10 transition duration-200">
                                        <i class="fas fa-user-circle mr-3 text-secondary"></i>
                                        Meu Perfil
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-3 text-white hover:bg-white/10 transition duration-200">
                                        <i class="fas fa-calendar-alt mr-3 text-secondary"></i>
                                        Agendamentos
                                    </a>
                                    <a href="#" class="flex items-center px-4 py-3 text-white hover:bg-white/10 transition duration-200">
                                        <i class="fas fa-cog mr-3 text-secondary"></i>
                                        Configurações
                                    </a>
                                    <div class="border-t border-white/10 mt-2"></div>
                                    <a href="#" onclick="logout()" class="flex items-center px-4 py-3 text-red-400 hover:bg-white/10 transition duration-200">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        Sair
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Botão Agendar -->
                            <button onclick="mostrarModalAgendamento()" class="btn-primary px-4 lg:px-6 py-3 rounded-full font-semibold text-white relative overflow-hidden whitespace-nowrap">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                <span class="hidden sm:inline">Agendar</span>
                            </button>
                        </div>
                    <?php else: ?>
                        <!-- Se não logado -->
                        <div class="flex items-center space-x-3">
                            <button onclick="mostrarModalLogin()" class="text-white hover:text-secondary transition duration-300 font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                <span class="hidden sm:inline">Entrar</span>
                            </button>
                            <button onclick="mostrarModalRegistro()" class="btn-primary px-4 lg:px-6 py-3 rounded-full font-semibold text-white relative overflow-hidden">
                                <i class="fas fa-user-plus mr-2"></i>
                                <span class="hidden sm:inline">Cadastrar</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Menu Mobile -->
                    <button class="md:hidden text-white hover:text-secondary ml-4" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu Mobile -->
        <div id="mobileMenu" class="md:hidden glass-effect border-t border-white/10 hidden">
            <div class="px-4 py-6 space-y-4">
                <a href="#home" class="block text-white hover:text-secondary transition duration-300 font-medium py-2">Início</a>
                <a href="#servicos" class="block text-white hover:text-secondary transition duration-300 font-medium py-2">Serviços</a>
                <a href="#sobre" class="block text-white hover:text-secondary transition duration-300 font-medium py-2">Sobre</a>
                <a href="#contato" class="block text-white hover:text-secondary transition duration-300 font-medium py-2">Contato</a>
                
                <?php if ($usuarioLogado): ?>
                    <div class="border-t border-white/10 pt-4">
                        <div class="text-white mb-4">
                            <p class="font-medium"><?= htmlspecialchars($usuarioNome) ?></p>
                            <p class="text-sm text-gray-300"><?= htmlspecialchars($usuarioEmail) ?></p>
                        </div>
                        <button onclick="mostrarModalAgendamento()" class="w-full btn-primary py-3 rounded-full font-semibold text-white mb-3">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Agendar Horário
                        </button>
                        <button onclick="logout()" class="w-full bg-red-600 text-white py-3 rounded-full font-semibold">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg min-h-screen flex items-center justify-center text-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="animate-slide-up">
                <h1 class="text-6xl md:text-8xl font-bold mb-6 leading-tight">
                    Estilo e
                    <span class="gradient-text block">Tradição</span>
                </h1>
                <p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-gray-300 leading-relaxed">
                    Mais que um corte, uma experiência única. Profissionais especializados, 
                    ambiente sofisticado e produtos premium.
                </p>
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <?php if (!$usuarioLogado): ?>
                        <button onclick="mostrarModalRegistro()" class="btn-primary px-10 py-4 rounded-full text-xl font-bold text-white relative overflow-hidden group">
                            <i class="fas fa-rocket mr-3"></i>
                            Começar Agora
                        </button>
                    <?php else: ?>
                        <button onclick="mostrarModalAgendamento()" class="btn-primary px-10 py-4 rounded-full text-xl font-bold text-white relative overflow-hidden group">
                            <i class="fas fa-calendar-plus mr-3"></i>
                            Agendar Agora
                        </button>
                    <?php endif; ?>
                    <button onclick="scrollToServices()" class="border-2 border-white/30 glass-effect text-white px-10 py-4 rounded-full text-xl font-bold hover:border-secondary hover:bg-secondary/10 transition duration-300">
                        <i class="fas fa-cut mr-3"></i>
                        Ver Serviços
                    </button>
                </div>
            </div>
            
            <!-- Elementos Decorativos -->
            <div class="absolute top-20 left-10 animate-float">
                <div class="w-20 h-20 border border-secondary/30 rounded-full"></div>
            </div>
            <div class="absolute bottom-20 right-10 animate-float" style="animation-delay: -3s;">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary/20 to-transparent rounded-full"></div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-secondary text-2xl"></i>
        </div>
    </section>

    <!-- Serviços Premium -->
    <section id="servicos" class="py-24 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-5xl font-bold text-primary mb-6">Nossos Serviços</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Cada serviço é uma obra de arte, executada com precisão e paixão por profissionais experientes.
                </p>
                <div class="w-24 h-1 bg-gradient-to-r from-secondary to-gold mx-auto mt-6"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($servicos)): ?>
                    <!-- Serviços padrão se não houver no banco -->
                    <div class="service-card bg-white rounded-3xl shadow-lg overflow-hidden group">
                        <div class="h-64 bg-gradient-to-br from-primary via-dark to-primary flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <i class="fas fa-cut text-6xl text-secondary relative z-10 group-hover:scale-110 transition duration-500"></i>
                            <div class="absolute top-4 right-4 bg-secondary/20 glass-effect px-3 py-1 rounded-full">
                                <span class="text-white text-sm font-medium">Premium</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-primary mb-3">Corte Clássico</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Corte tradicional com acabamento impecável, incluindo lavagem e finalização com produtos premium.
                            </p>
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-3xl font-bold gradient-text">R$ 45,00</span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    45min
                                </span>
                            </div>
                            <button onclick="agendarServico(1)" class="w-full bg-primary text-white py-3 rounded-2xl hover:bg-dark transition duration-300 font-semibold group-hover:bg-secondary">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Agendar Serviço
                            </button>
                        </div>
                    </div>

                    <div class="service-card bg-white rounded-3xl shadow-lg overflow-hidden group">
                        <div class="h-64 bg-gradient-to-br from-primary via-dark to-primary flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <i class="fas fa-magic text-6xl text-secondary relative z-10 group-hover:scale-110 transition duration-500"></i>
                            <div class="absolute top-4 right-4 bg-accent/20 glass-effect px-3 py-1 rounded-full">
                                <span class="text-white text-sm font-medium">Exclusivo</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-primary mb-3">Barba + Bigode</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Aparagem e modelagem profissional da barba e bigode com produtos especializados e toalha quente.
                            </p>
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-3xl font-bold gradient-text">R$ 35,00</span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    30min
                                </span>
                            </div>
                            <button onclick="agendarServico(2)" class="w-full bg-primary text-white py-3 rounded-2xl hover:bg-dark transition duration-300 font-semibold group-hover:bg-secondary">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Agendar Serviço
                            </button>
                        </div>
                    </div>

                    <div class="service-card bg-white rounded-3xl shadow-lg overflow-hidden group">
                        <div class="h-64 bg-gradient-to-br from-primary via-dark to-primary flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <i class="fas fa-crown text-6xl text-secondary relative z-10 group-hover:scale-110 transition duration-500"></i>
                            <div class="absolute top-4 right-4 bg-gold/20 glass-effect px-3 py-1 rounded-full">
                                <span class="text-white text-sm font-medium">VIP</span>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-primary mb-3">Pacote Completo</h3>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                Experiência completa: corte, barba, sobrancelha, lavagem e relaxamento. O melhor que oferecemos.
                            </p>
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-3xl font-bold gradient-text">R$ 75,00</span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    90min
                                </span>
                            </div>
                            <button onclick="agendarServico(3)" class="w-full bg-primary text-white py-3 rounded-2xl hover:bg-dark transition duration-300 font-semibold group-hover:bg-secondary">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                Agendar Serviço
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Serviços do banco de dados -->
                    <?php foreach ($servicos as $servico): ?>
                        <div class="service-card bg-white rounded-3xl shadow-lg overflow-hidden group">
                            <div class="h-64 bg-gradient-to-br from-primary via-dark to-primary flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-black/20"></div>
                                <?php if (!empty($servico['imagem'])): ?>
                                    <img src="<?= htmlspecialchars($servico['imagem']) ?>" alt="<?= htmlspecialchars($servico['nome']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-cut text-6xl text-secondary relative z-10 group-hover:scale-110 transition duration-500"></i>
                                <?php endif; ?>
                                <div class="absolute top-4 right-4 bg-secondary/20 glass-effect px-3 py-1 rounded-full">
                                    <span class="text-white text-sm font-medium">Premium</span>
                                </div>
                            </div>
                            <div class="p-8">
                                <h3 class="text-2xl font-bold text-primary mb-3"><?= htmlspecialchars($servico['nome']) ?></h3>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    <?= htmlspecialchars($servico['descricao']) ?>
                                </p>
                                <div class="flex justify-between items-center mb-6">
                                    <span class="text-3xl font-bold gradient-text">R$ <?= number_format($servico['preco'], 2, ',', '.') ?></span>
                                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= $servico['duracao'] ?>min
                                    </span>
                                </div>
                                <button onclick="agendarServico(<?= $servico['id'] ?>)" class="w-full bg-primary text-white py-3 rounded-2xl hover:bg-dark transition duration-300 font-semibold group-hover:bg-secondary">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Agendar Serviço
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Modal de Login Moderno -->
    <div id="modalLogin" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl max-w-md w-full mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="loginModal">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary to-gold rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-primary">Bem-vindo de volta!</h2>
                <p class="text-gray-600 mt-2">Faça login para agendar seus horários</p>
            </div>
            
            <form id="formLogin" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="seu@email.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Senha</label>
                    <input type="password" name="senha" required 
                           class="w-full px-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="••••••••">
                </div>
                <button type="submit" 
                        class="w-full btn-primary py-4 rounded-2xl font-bold text-white text-lg relative overflow-hidden">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Entrar
                </button>
            </form>
            
            <div class="text-center mt-6">
                <button onclick="mostrarModalRegistro(); fecharModal('modalLogin')" 
                        class="text-secondary hover:text-gold transition duration-300 font-semibold">
                    Não tem conta? Cadastre-se aqui
                </button>
            </div>
            
            <button onclick="fecharModal('modalLogin')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition duration-300">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Modal de Registro Moderno -->
    <div id="modalRegistro" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl max-w-md w-full mx-4 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="registroModal">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary to-gold rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="text-3xl font-bold text-primary">Junte-se a nós!</h2>
                <p class="text-gray-600 mt-2">Crie sua conta e tenha acesso exclusivo</p>
            </div>
            
            <form id="formRegistro" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome Completo</label>
                    <input type="text" name="nome" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="Seu nome completo">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="seu@email.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Telefone</label>
                    <input type="tel" name="telefone" 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="(11) 99999-9999">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Senha</label>
                    <input type="password" name="senha" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                           placeholder="••••••••">
                </div>
                <button type="submit" 
                        class="w-full btn-primary py-4 rounded-2xl font-bold text-white text-lg relative overflow-hidden mt-6">
                    <i class="fas fa-user-plus mr-2"></i>
                    Criar Conta
                </button>
            </form>
            
            <div class="text-center mt-6">
                <button onclick="mostrarModalLogin(); fecharModal('modalRegistro')" 
                        class="text-secondary hover:text-gold transition duration-300 font-semibold">
                    Já tem conta? Faça login aqui
                </button>
            </div>
            
            <button onclick="fecharModal('modalRegistro')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition duration-300">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
    </div>

   <div id="modalAgendamento" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-5xl w-full shadow-2xl transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto" id="agendamentoModal">
            <div class="sticky top-0 bg-white p-6 border-b border-gray-100 rounded-t-3xl">
                <div class="text-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-secondary to-gold rounded-2xl flex items-center justify-center mx-auto mb-3 animate-glow">
                        <i class="fas fa-calendar-plus text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-primary">Agendar Horário</h2>
                    <p class="text-gray-600 text-sm mt-1">Escolha o barbeiro, serviço, data e horário</p>
                </div>
                
                <button onclick="fecharModal('modalAgendamento')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-6">
                <form id="formAgendamento" class="space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Coluna Esquerda -->
                        <div class="space-y-5">
                            <!-- Seleção de Barbeiro -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user-tie text-secondary mr-2"></i>
                                    Barbeiro
                                </label>
                                <div id="barbeirosContainer" class="space-y-2 max-h-32 overflow-y-auto">
                                    <!-- Loading -->
                                    <div class="animate-pulse space-y-2">
                                        <div class="bg-gray-200 rounded-xl p-3 h-16"></div>
                                        <div class="bg-gray-200 rounded-xl p-3 h-16"></div>
                                    </div>
                                </div>
                                <input type="hidden" name="barbeiro_id" id="barbeiroSelecionado" required>
                            </div>

                            <!-- Seleção de Serviço -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-cut text-secondary mr-2"></i>
                                    Serviço
                                </label>
                                <select name="servico_id" required 
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300 text-sm"
                                        onchange="atualizarResumoServico()">
                                    <option value="">Selecione um serviço</option>
                                    <?php if (!empty($servicos)): ?>
                                        <?php foreach ($servicos as $servico): ?>
                                            <option value="<?= $servico['id'] ?>" data-duracao="<?= $servico['duracao'] ?>" data-preco="<?= $servico['preco'] ?>">
                                                <?= htmlspecialchars($servico['nome']) ?> - R$ <?= number_format($servico['preco'], 2, ',', '.') ?> (<?= $servico['duracao'] ?>min)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="1" data-duracao="45" data-preco="45.00">Corte Clássico - R$ 45,00 (45min)</option>
                                        <option value="2" data-duracao="30" data-preco="35.00">Barba + Bigode - R$ 35,00 (30min)</option>
                                        <option value="3" data-duracao="90" data-preco="75.00">Pacote Completo - R$ 75,00 (90min)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <!-- Data -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar text-secondary mr-2"></i>
                                    Data
                                </label>
                                <input type="date" name="data" required 
                                       min="<?= date('Y-m-d') ?>"
                                       max="<?= date('Y-m-d', strtotime('+30 days')) ?>"
                                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300"
                                       onchange="atualizarHorariosAgendamento()">
                            </div>
                            
                            <!-- Observações -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-comment text-secondary mr-2"></i>
                                    Observações (opcional)
                                </label>
                                <textarea name="observacoes" rows="2" 
                                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-secondary focus:border-transparent transition duration-300 resize-none text-sm"
                                          placeholder="Alguma observação especial..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Coluna Direita -->
                        <div class="space-y-5">
                            <!-- Horários Disponíveis -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-clock text-secondary mr-2"></i>
                                    Horários Disponíveis
                                    <span id="loadingHorarios" class="text-xs text-gray-500 ml-2 hidden">
                                        <i class="fas fa-spinner fa-spin"></i> Carregando...
                                    </span>
                                </label>
                                <div id="horariosDisponiveis" class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto p-2 border border-gray-200 rounded-xl">
                                    <div class="col-span-3 text-center text-gray-500 py-6">
                                        <i class="fas fa-info-circle mb-2 text-xl"></i>
                                        <p class="text-sm">Selecione barbeiro, serviço e data</p>
                                    </div>
                                </div>
                                <input type="hidden" name="horario" id="horarioSelecionado" required>
                            </div>
                            
                            <!-- Resumo Compacto -->
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-xl">
                                <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-receipt text-secondary mr-2"></i>
                                    Resumo
                                </h3>
                                
                                <div class="space-y-3">
                                    <!-- Barbeiro -->
                                    <div id="resumoBarbeiro" class="hidden bg-white p-3 rounded-lg border border-gray-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-secondary to-gold rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-tie text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Barbeiro</p>
                                                <p class="font-bold text-gray-800 text-sm" id="resumoBarbeiroNome">-</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Serviço -->
                                    <div id="resumoServico" class="hidden bg-white p-3 rounded-lg border border-gray-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-primary to-dark rounded-full flex items-center justify-center">
                                                <i class="fas fa-cut text-white text-sm"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs text-gray-600">Serviço</p>
                                                <p class="font-bold text-gray-800 text-sm" id="resumoServicoNome">-</p>
                                                <p class="text-xs text-gray-600">
                                                    <span id="resumoServicoPreco">R$ 0,00</span> • 
                                                    <span id="resumoServicoDuracao">0min</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Data e horário -->
                                    <div id="resumoDateTime" class="hidden bg-white p-3 rounded-lg border border-gray-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-calendar-check text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-600">Agendamento</p>
                                                <p class="font-bold text-gray-800 text-sm" id="resumoData">-</p>
                                                <p class="text-xs text-gray-600" id="resumoHorario">-</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Total -->
                                    <div id="resumoTotal" class="hidden bg-gradient-to-r from-secondary to-gold p-3 rounded-lg text-white text-center">
                                        <p class="text-xs opacity-90">Total</p>
                                        <p class="text-xl font-bold" id="resumoPrecoTotal">R$ 0,00</p>
                                    </div>
                                </div>
                                
                                <!-- Info compacta -->
                                <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-xs text-blue-700">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Chegue 10min antes • Cancelamento até 2h antes
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="flex space-x-4 pt-4 border-t border-gray-100">
                        <button type="button" onclick="fecharModal('modalAgendamento')" 
                                class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:border-gray-400 hover:bg-gray-50 transition duration-300">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="flex-1 btn-primary px-6 py-3 rounded-xl font-bold text-white relative overflow-hidden">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Confirmar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast de Notificação Moderno -->
    <div id="toast" class="fixed top-8 right-8 z-50 transform translate-x-full transition-transform duration-500">
        <div class="glass-effect rounded-2xl shadow-2xl p-6 flex items-center space-x-4 min-w-80">
            <div class="w-12 h-12 rounded-full flex items-center justify-center bg-green-500">
                <i class="fas fa-check text-white"></i>
            </div>
            <div>
                <h4 class="text-white font-semibold">Sucesso!</h4>
                <p id="toastMessage" class="text-gray-200 text-sm">Mensagem de sucesso</p>
            </div>
            <button onclick="fecharToast()" class="text-gray-300 hover:text-white ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Footer Moderno -->
    <footer class="bg-primary text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary via-dark to-primary"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-gold rounded-xl flex items-center justify-center">
                            <i class="fas fa-cut text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">BarberShop Elite</h3>
                            <span class="text-secondary font-medium">Tradição e Excelência</span>
                        </div>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-6 text-lg">
                        A melhor barbearia da região, oferecendo serviços de qualidade superior 
                        com profissionais experientes e ambiente sofisticado. Sua satisfação é nossa prioridade.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-secondary transition duration-300">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-secondary transition duration-300">
                            <i class="fab fa-facebook text-white"></i>
                        </a>
                        <a href="#" class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-secondary transition duration-300">
                            <i class="fab fa-whatsapp text-white"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xl font-bold mb-6 text-secondary">Contato</h4>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-secondary/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-secondary text-sm"></i>
                            </div>
                            <span class="text-gray-300">Rua das Flores, 123 - Centro</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-secondary/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-secondary text-sm"></i>
                            </div>
                            <span class="text-gray-300">(11) 3333-4444</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-secondary/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-secondary text-sm"></i>
                            </div>
                            <span class="text-gray-300">contato@barbershopelite.com</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-xl font-bold mb-6 text-secondary">Horário</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Segunda - Sexta</span>
                            <span class="text-white font-medium">8h às 18h</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Sábado</span>
                            <span class="text-white font-medium">8h às 16h</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Domingo</span>
                            <span class="text-red-400 font-medium">Fechado</span>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <button onclick="mostrarModalAgendamento()" class="btn-primary px-6 py-3 rounded-full font-semibold text-white w-full relative overflow-hidden">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Agendar Agora
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-300 mb-4 md:mb-0">
                        &copy; 2024 BarberShop Elite. Todos os direitos reservados.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-300 hover:text-secondary transition duration-300">Política de Privacidade</a>
                        <a href="#" class="text-gray-300 hover:text-secondary transition duration-300">Termos de Uso</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Variáveis globais
        let barbeirosDisponiveis = [];
        let barbeiroSelecionadoId = null;
        
        // Carregar barbeiros quando o modal abre
        async function carregarBarbeiros() {
            try {
                const response = await fetch('api/agendamentos.php?acao=barbeiros');
                const data = await response.json();
                
                if (data.success) {
                    barbeirosDisponiveis = data.data;
                    renderizarBarbeiros();
                } else {
                    console.log('Nenhum barbeiro encontrado, usando dados de exemplo');
                    // Usar dados de exemplo se a API não estiver disponível
                    barbeirosDisponiveis = [
                        {
                            id: 1,
                            nome: 'João Silva',
                            especialidades: 'Cortes Clássicos',
                            horario_inicio: '08:00',
                            horario_fim: '18:00'
                        },
                        {
                            id: 2,
                            nome: 'Pedro Santos',
                            especialidades: 'Barbas e Bigodes',
                            horario_inicio: '09:00',
                            horario_fim: '17:00'
                        }
                    ];
                    renderizarBarbeiros();
                }
            } catch (error) {
                console.error('Erro ao carregar barbeiros:', error);
                // Usar dados de exemplo em caso de erro
                barbeirosDisponiveis = [
                    {
                        id: 1,
                        nome: 'João Silva',
                        especialidades: 'Cortes Clássicos',
                        horario_inicio: '08:00',
                        horario_fim: '18:00'
                    },
                    {
                        id: 2,
                        nome: 'Pedro Santos',
                        especialidades: 'Barbas e Bigodes',
                        horario_inicio: '09:00',
                        horario_fim: '17:00'
                    }
                ];
                renderizarBarbeiros();
            }
        }

        // Renderizar lista de barbeiros
        function renderizarBarbeiros() {
            const container = document.getElementById('barbeirosContainer');
            
            if (barbeirosDisponiveis.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center text-gray-500 py-4">
                        <i class="fas fa-user-slash mb-2 text-2xl"></i>
                        <p>Nenhum barbeiro disponível no momento</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = barbeirosDisponiveis.map(barbeiro => `
                <div class="barbeiro-card bg-white border-2 border-gray-200 rounded-2xl p-4 cursor-pointer transition duration-300 hover:border-secondary hover:shadow-lg" 
                     data-barbeiro-id="${barbeiro.id}" onclick="selecionarBarbeiro(${barbeiro.id}, '${barbeiro.nome}')">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-secondary to-gold rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">${barbeiro.nome}</h4>
                            <p class="text-sm text-gray-600">${barbeiro.especialidades || 'Especialista'}</p>
                            <div class="flex items-center text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock mr-1"></i>
                                ${barbeiro.horario_inicio} às ${barbeiro.horario_fim}
                            </div>
                        </div>
                        <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center barbeiro-check">
                            <i class="fas fa-check text-white text-xs hidden"></i>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Selecionar barbeiro
        function selecionarBarbeiro(barbeiroId, barbeiroNome) {
            // Remover seleção anterior
            document.querySelectorAll('.barbeiro-card').forEach(card => {
                card.classList.remove('border-secondary', 'bg-secondary/5');
                card.classList.add('border-gray-200');
                const check = card.querySelector('.barbeiro-check');
                check.classList.remove('bg-secondary', 'border-secondary');
                check.classList.add('border-gray-300');
                check.querySelector('i').classList.add('hidden');
            });
            
            // Adicionar seleção atual
            const cardSelecionado = document.querySelector(`[data-barbeiro-id="${barbeiroId}"]`);
            if (cardSelecionado) {
                cardSelecionado.classList.remove('border-gray-200');
                cardSelecionado.classList.add('border-secondary', 'bg-secondary/5');
                
                const check = cardSelecionado.querySelector('.barbeiro-check');
                check.classList.remove('border-gray-300');
                check.classList.add('bg-secondary', 'border-secondary');
                check.querySelector('i').classList.remove('hidden');
            }
            
            // Definir valores
            barbeiroSelecionadoId = barbeiroId;
            document.getElementById('barbeiroSelecionado').value = barbeiroId;
            
            // Atualizar resumo
            document.getElementById('resumoBarbeiroNome').textContent = barbeiroNome;
            document.getElementById('resumoBarbeiro').classList.remove('hidden');
            
            // Atualizar horários se já tiver data e serviço selecionados
            atualizarHorariosAgendamento();
        }

        // Login com redirecionamento
        document.getElementById('formLogin').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('acao', 'login');try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Login realizado com sucesso!', 'success');
                    fecharModal('modalLogin');
                    
                    // Aguardar um momento antes de recarregar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarToast(data.message || 'Erro ao fazer login', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarToast('Erro de conexão. Tente novamente.', 'error');
            }
        });

        // Registro
        document.getElementById('formRegistro').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const senha = formData.get('senha');
            const confirmarSenha = formData.get('confirmar_senha');
            
            if (senha !== confirmarSenha) {
                mostrarToast('As senhas não coincidem!', 'error');
                return;
            }
            
            formData.append('acao', 'registro');
            
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Conta criada com sucesso!', 'success');
                    fecharModal('modalRegistro');
                    
                    // Aguardar um momento antes de recarregar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    mostrarToast(data.message || 'Erro ao criar conta', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarToast('Erro de conexão. Tente novamente.', 'error');
            }
        });

        // Agendamento
        document.getElementById('formAgendamento').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!usuarioLogado) {
                mostrarToast('Faça login para agendar!', 'error');
                fecharModal('modalAgendamento');
                mostrarModalLogin();
                return;
            }
            
            const formData = new FormData(this);
            formData.append('acao', 'criar_agendamento');
            
            try {
                const response = await fetch('api/agendamentos.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Agendamento realizado com sucesso!', 'success');
                    fecharModal('modalAgendamento');
                    this.reset();
                    limparResumoAgendamento();
                } else {
                    mostrarToast(data.message || 'Erro ao agendar', 'error');
                }
            } catch (error) {
                console.error('Erro:', error);
                mostrarToast('Erro de conexão. Tente novamente.', 'error');
            }
        });

        // Atualizar horários disponíveis
        async function atualizarHorariosAgendamento() {
            const barbeiroId = document.getElementById('barbeiroSelecionado').value;
            const data = document.querySelector('input[name="data"]').value;
            const servicoSelect = document.querySelector('select[name="servico_id"]');
            const servicoId = servicoSelect.value;
            
            if (!barbeiroId || !data || !servicoId) {
                document.getElementById('horariosDisponiveis').innerHTML = `
                    <div class="col-span-3 text-center text-gray-500 py-6">
                        <i class="fas fa-info-circle mb-2 text-xl"></i>
                        <p class="text-sm">Selecione barbeiro, serviço e data</p>
                    </div>
                `;
                return;
            }
            
            // Mostrar loading
            document.getElementById('loadingHorarios').classList.remove('hidden');
            document.getElementById('horariosDisponiveis').innerHTML = `
                <div class="col-span-3 flex justify-center py-6">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-secondary"></div>
                </div>
            `;
            
            try {
                const response = await fetch(`api/agendamentos.php?acao=horarios_disponiveis&barbeiro_id=${barbeiroId}&data=${data}&servico_id=${servicoId}`);
                const result = await response.json();
                
                if (result.success && result.data.length > 0) {
                    renderizarHorarios(result.data);
                } else {
                    // Gerar horários padrão se a API não estiver disponível
                    const horariosPadrao = gerarHorariosPadrao();
                    renderizarHorarios(horariosPadrao);
                }
            } catch (error) {
                console.error('Erro ao carregar horários:', error);
                // Usar horários padrão em caso de erro
                const horariosPadrao = gerarHorariosPadrao();
                renderizarHorarios(horariosPadrao);
            } finally {
                document.getElementById('loadingHorarios').classList.add('hidden');
            }
        }

        // Gerar horários padrão
        function gerarHorariosPadrao() {
            const horarios = [];
            const inicio = 8; // 8h
            const fim = 18; // 18h
            const intervalo = 30; // 30 minutos
            
            for (let hora = inicio; hora < fim; hora++) {
                for (let minuto = 0; minuto < 60; minuto += intervalo) {
                    const horarioStr = `${hora.toString().padStart(2, '0')}:${minuto.toString().padStart(2, '0')}`;
                    horarios.push({
                        horario: horarioStr,
                        disponivel: Math.random() > 0.3 // 70% dos horários disponíveis
                    });
                }
            }
            
            return horarios;
        }

        // Renderizar horários
        function renderizarHorarios(horarios) {
            const container = document.getElementById('horariosDisponiveis');
            
            if (horarios.length === 0) {
                container.innerHTML = `
                    <div class="col-span-3 text-center text-gray-500 py-6">
                        <i class="fas fa-times-circle mb-2 text-xl text-red-500"></i>
                        <p class="text-sm">Nenhum horário disponível para esta data</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = horarios.map(horario => {
                const disponivel = horario.disponivel;
                const classes = disponivel 
                    ? 'bg-white border-2 border-gray-200 text-gray-800 hover:border-secondary hover:bg-secondary/5 cursor-pointer'
                    : 'bg-gray-100 border-2 border-gray-300 text-gray-400 cursor-not-allowed';
                    
                return `
                    <button type="button" 
                            class="horario-btn p-3 rounded-xl text-sm font-semibold transition duration-300 ${classes}"
                            data-horario="${horario.horario}"
                            ${disponivel ? `onclick="selecionarHorario('${horario.horario}')"` : 'disabled'}>
                        ${horario.horario}
                    </button>
                `;
            }).join('');
        }

        // Selecionar horário
        function selecionarHorario(horario) {
            // Remover seleção anterior
            document.querySelectorAll('.horario-btn').forEach(btn => {
                btn.classList.remove('border-secondary', 'bg-secondary', 'text-white');
                btn.classList.add('border-gray-200', 'text-gray-800');
            });
            
            // Adicionar seleção atual
            const btnSelecionado = document.querySelector(`[data-horario="${horario}"]`);
            if (btnSelecionado) {
                btnSelecionado.classList.remove('border-gray-200', 'text-gray-800');
                btnSelecionado.classList.add('border-secondary', 'bg-secondary', 'text-white');
            }
            
            // Definir valor
            document.getElementById('horarioSelecionado').value = horario;
            
            // Atualizar resumo
            atualizarResumoDateTime();
        }

        // Atualizar resumo do serviço
        function atualizarResumoServico() {
            const servicoSelect = document.querySelector('select[name="servico_id"]');
            const opcaoSelecionada = servicoSelect.options[servicoSelect.selectedIndex];
            
            if (servicoSelect.value && opcaoSelecionada) {
                const nome = opcaoSelecionada.text.split(' - ')[0];
                const preco = opcaoSelecionada.dataset.preco;
                const duracao = opcaoSelecionada.dataset.duracao;
                
                document.getElementById('resumoServicoNome').textContent = nome;
                document.getElementById('resumoServicoPreco').textContent = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
                document.getElementById('resumoServicoDuracao').textContent = `${duracao}min`;
                document.getElementById('resumoPrecoTotal').textContent = `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
                
                document.getElementById('resumoServico').classList.remove('hidden');
                document.getElementById('resumoTotal').classList.remove('hidden');
            } else {
                document.getElementById('resumoServico').classList.add('hidden');
                document.getElementById('resumoTotal').classList.add('hidden');
            }
            
            // Atualizar horários quando mudar o serviço
            atualizarHorariosAgendamento();
        }

        // Atualizar resumo de data e horário
        function atualizarResumoDateTime() {
            const data = document.querySelector('input[name="data"]').value;
            const horario = document.getElementById('horarioSelecionado').value;
            
            if (data && horario) {
                const dataFormatada = new Date(data + 'T00:00:00').toLocaleDateString('pt-BR');
                
                document.getElementById('resumoData').textContent = dataFormatada;
                document.getElementById('resumoHorario').textContent = horario;
                document.getElementById('resumoDateTime').classList.remove('hidden');
            } else {
                document.getElementById('resumoDateTime').classList.add('hidden');
            }
        }

        // Limpar resumo do agendamento
        function limparResumoAgendamento() {
            document.getElementById('resumoBarbeiro').classList.add('hidden');
            document.getElementById('resumoServico').classList.add('hidden');
            document.getElementById('resumoDateTime').classList.add('hidden');
            document.getElementById('resumoTotal').classList.add('hidden');
            
            barbeiroSelecionadoId = null;
            document.getElementById('barbeiroSelecionado').value = '';
            document.getElementById('horarioSelecionado').value = '';
            
            // Limpar seleções visuais
            document.querySelectorAll('.barbeiro-card').forEach(card => {
                card.classList.remove('border-secondary', 'bg-secondary/5');
                card.classList.add('border-gray-200');
                const check = card.querySelector('.barbeiro-check');
                check.classList.remove('bg-secondary', 'border-secondary');
                check.classList.add('border-gray-300');
                check.querySelector('i').classList.add('hidden');
            });
            
            document.querySelectorAll('.horario-btn').forEach(btn => {
                btn.classList.remove('border-secondary', 'bg-secondary', 'text-white');
                btn.classList.add('border-gray-200', 'text-gray-800');
            });
        }

        // Funções de modal
        function mostrarModalLogin() {
            const modal = document.getElementById('modalLogin');
            const modalContent = document.getElementById('loginModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function mostrarModalRegistro() {
            const modal = document.getElementById('modalRegistro');
            const modalContent = document.getElementById('registroModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function mostrarModalAgendamento() {
            if (!usuarioLogado) {
                mostrarToast('Faça login para agendar!', 'error');
                mostrarModalLogin();
                return;
            }
            
            const modal = document.getElementById('modalAgendamento');
            const modalContent = document.getElementById('agendamentoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Carregar barbeiros quando abrir o modal
            carregarBarbeiros();
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function fecharModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContent = modal.querySelector('.transform');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                
                // Limpar formulários
                if (modalId === 'modalAgendamento') {
                    document.getElementById('formAgendamento').reset();
                    limparResumoAgendamento();
                } else if (modalId === 'modalLogin') {
                    document.getElementById('formLogin').reset();
                } else if (modalId === 'modalRegistro') {
                    document.getElementById('formRegistro').reset();
                }
            }, 300);
        }

        // Fechar modais clicando fora
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('fixed') && e.target.classList.contains('inset-0')) {
                const modalId = e.target.id;
                if (modalId && modalId.startsWith('modal')) {
                    fecharModal(modalId);
                }
            }
        });

        // Toast de notificação
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const icon = toast.querySelector('i');
            const iconContainer = toast.querySelector('.w-12');
            
            toastMessage.textContent = mensagem;
            
            // Definir cores baseado no tipo
            if (tipo === 'success') {
                iconContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center bg-green-500';
                icon.className = 'fas fa-check text-white';
            } else if (tipo === 'error') {
                iconContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center bg-red-500';
                icon.className = 'fas fa-times text-white';
            } else if (tipo === 'warning') {
                iconContainer.className = 'w-12 h-12 rounded-full flex items-center justify-center bg-yellow-500';
                icon.className = 'fas fa-exclamation-triangle text-white';
            }
            
            // Mostrar toast
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            // Auto fechar após 5 segundos
            setTimeout(() => {
                fecharToast();
            }, 5000);
        }

        function fecharToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
        }

        // Função de logout
        async function logout() {
            try {
                const response = await fetch('api/usuarios.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'acao=logout'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarToast('Logout realizado com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (error) {
                console.error('Erro:', error);
                // Recarregar mesmo em caso de erro
                window.location.reload();
            }
        }

        // Dropdown do usuário
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('userMenuButton');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userMenuButton && userDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    if (userDropdown.classList.contains('hidden')) {
                        userDropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                        userDropdown.classList.add('opacity-100', 'scale-100');
                    } else {
                        userDropdown.classList.remove('opacity-100', 'scale-100');
                        userDropdown.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => {
                            userDropdown.classList.add('hidden');
                        }, 200);
                    }
                });
                
                // Fechar dropdown clicando fora
                document.addEventListener('click', function() {
                    if (!userDropdown.classList.contains('hidden')) {
                        userDropdown.classList.remove('opacity-100', 'scale-100');
                        userDropdown.classList.add('opacity-0', 'scale-95');
                        setTimeout(() => {
                            userDropdown.classList.add('hidden');
                        }, 200);
                    }
                });
            }
        });

        // Menu mobile
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Scroll suave para seções
        function scrollToServices() {
            document.getElementById('servicos').scrollIntoView({ behavior: 'smooth' });
        }

        // Agendar serviço específico
        function agendarServico(servicoId) {
            if (!usuarioLogado) {
                mostrarToast('Faça login para agendar!', 'error');
                mostrarModalLogin();
                return;
            }
            
            mostrarModalAgendamento();
            
            // Aguardar o modal abrir e então selecionar o serviço
            setTimeout(() => {
                const servicoSelect = document.querySelector('select[name="servico_id"]');
                if (servicoSelect) {
                    servicoSelect.value = servicoId;
                    atualizarResumoServico();
                }
            }, 500);
        }

        // Navbar transparente no scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 50) {
                navbar.classList.add('bg-primary/95');
            } else {
                navbar.classList.remove('bg-primary/95');
            }
        });

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            });

            document.querySelectorAll('.service-card').forEach(card => {
                observer.observe(card);
            });
        });

        // Atualizar data quando mudar
        document.addEventListener('DOMContentLoaded', function() {
            const dataInput = document.querySelector('input[name="data"]');
            if (dataInput) {
                dataInput.addEventListener('change', function() {
                    atualizarResumoDateTime();
                    atualizarHorariosAgendamento();
                });
            }
        });
    </script>
</body>
</html>