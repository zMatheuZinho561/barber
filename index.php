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
                <div class="flex items-center space-x-4">
                    <!-- Se logado -->
                    <div class="hidden" id="userLoggedIn">
                        <div class="relative">
                            <button id="userMenuButton" class="flex items-center space-x-3 text-white hover:text-secondary transition duration-300 bg-white/10 px-4 py-2 rounded-full glass-effect">
                                <div class="w-8 h-8 bg-gradient-to-br from-secondary to-gold rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden md:block font-medium">João Silva</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            
                            <!-- Dropdown Moderno -->
                            <div id="userDropdown" class="absolute right-0 mt-3 w-64 glass-effect rounded-2xl shadow-2xl py-2 hidden transform opacity-0 scale-95 transition-all duration-200">
                                <div class="px-4 py-3 border-b border-white/10">
                                    <p class="text-white font-medium">João Silva</p>
                                    <p class="text-gray-300 text-sm">joao@email.com</p>
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
                        
                        <button onclick="mostrarModalAgendamento()" class="btn-primary px-6 py-3 rounded-full font-semibold text-white relative overflow-hidden">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Agendar
                        </button>
                    </div>
                    
                    <!-- Se não logado -->
                    <div class="flex items-center space-x-3" id="userNotLoggedIn">
                        <button onclick="mostrarModalLogin()" class="text-white hover:text-secondary transition duration-300 font-medium">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Entrar
                        </button>
                        <button onclick="mostrarModalRegistro()" class="btn-primary px-6 py-3 rounded-full font-semibold text-white relative overflow-hidden">
                            <i class="fas fa-user-plus mr-2"></i>
                            Cadastrar
                        </button>
                    </div>
                    
                    <!-- Menu Mobile -->
                    <button class="md:hidden text-white hover:text-secondary" onclick="toggleMobileMenu()">
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
            </div>
        </div>
    </nav>

    <!-- Hero Section Reimaginado -->
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
                    <button onclick="mostrarModalRegistro()" class="btn-primary px-10 py-4 rounded-full text-xl font-bold text-white relative overflow-hidden group">
                        <i class="fas fa-rocket mr-3"></i>
                        Começar Agora
                    </button>
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
                <!-- Serviço 1 -->
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

                <!-- Serviço 2 -->
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

                <!-- Serviço 3 -->
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
        // Controle do dropdown do usuário
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('userMenuButton');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userMenuButton && userDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isHidden = userDropdown.classList.contains('hidden');
                    
                    if (isHidden) {
                        userDropdown.classList.remove('hidden');
                        setTimeout(() => {
                            userDropdown.classList.remove('opacity-0', 'scale-95');
                            userDropdown.classList.add('opacity-100', 'scale-100');
                        }, 10);
                    } else {
                        userDropdown.classList.add('opacity-0', 'scale-95');
                        userDropdown.classList.remove('opacity-100', 'scale-100');
                        setTimeout(() => {
                            userDropdown.classList.add('hidden');
                        }, 200);
                    }
                });
                
                // Fechar dropdown ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.add('opacity-0', 'scale-95');
                        userDropdown.classList.remove('opacity-100', 'scale-100');
                        setTimeout(() => {
                            userDropdown.classList.add('hidden');
                        }, 200);
                    }
                });
            }

            // Efeito da navbar no scroll
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('nav');
                if (window.scrollY > 100) {
                    navbar.style.backdropFilter = 'blur(20px)';
                    navbar.style.background = 'rgba(15, 23, 42, 0.95)';
                } else {
                    navbar.style.background = 'rgba(15, 23, 42, 0.9)';
                }
            });
        });

        // Toggle mobile menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Funções dos modais
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

        function fecharModal(modalId) {
            const modal = document.getElementById(modalId);
            const modalContent = modal.querySelector('div > div');
            
            modalContent.classList.add('scale-95', 'opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        function scrollToServices() {
            document.getElementById('servicos').scrollIntoView({ behavior: 'smooth' });
        }

        // Toast de notificação
        function mostrarToast(mensagem, tipo = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const icon = toast.querySelector('i');
            const iconContainer = toast.querySelector('.w-12');
            
            toastMessage.textContent = mensagem;
            
            // Definir cor e ícone baseado no tipo
            if (tipo === 'error') {
                iconContainer.className = iconContainer.className.replace('bg-green-500', 'bg-red-500');
                icon.className = 'fas fa-times text-white';
            } else if (tipo === 'warning') {
                iconContainer.className = iconContainer.className.replace('bg-green-500', 'bg-yellow-500');
                icon.className = 'fas fa-exclamation text-white';
            } else {
                iconContainer.className = iconContainer.className.replace(/bg-\w+-500/, 'bg-green-500');
                icon.className = 'fas fa-check text-white';
            }
            
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
            
            // Auto fechar após 5 segundos
            setTimeout(() => {
                fecharToast();
            }, 5000);
        }

        function fecharToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('translate-x-full');
            toast.classList.remove('translate-x-0');
        }

        // Login
        document.getElementById('formLogin').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                // Simular resposta de API
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Simular login bem-sucedido
                mostrarToast('Login realizado com sucesso!', 'success');
                fecharModal('modalLogin');
                
                // Simular mudança de estado do usuário
                setTimeout(() => {
                    document.getElementById('userNotLoggedIn').classList.add('hidden');
                    document.getElementById('userLoggedIn').classList.remove('hidden');
                }, 1000);
                
            } catch (error) {
                mostrarToast('Erro ao realizar login', 'error');
                console.error('Erro:', error);
            }
        });

        // Registro
        document.getElementById('formRegistro').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Validar senhas
            if (formData.get('senha') !== formData.get('confirmar_senha')) {
                mostrarToast('As senhas não conferem!', 'error');
                return;
            }
            
            try {
                // Simular resposta de API
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                mostrarToast('Cadastro realizado com sucesso!', 'success');
                fecharModal('modalRegistro');
                setTimeout(() => {
                    mostrarModalLogin();
                }, 1500);
                
            } catch (error) {
                mostrarToast('Erro ao realizar cadastro', 'error');
                console.error('Erro:', error);
            }
        });

        // Logout
        async function logout() {
            try {
                mostrarToast('Logout realizado com sucesso!', 'success');
                setTimeout(() => {
                    document.getElementById('userLoggedIn').classList.add('hidden');
                    document.getElementById('userNotLoggedIn').classList.remove('hidden');
                }, 1500);
            } catch (error) {
                mostrarToast('Erro ao fazer logout', 'error');
                console.error('Erro:', error);
            }
        }

        // Funções placeholder
        function mostrarModalAgendamento() {
            mostrarToast('Modal de agendamento será implementado em breve!', 'warning');
        }

        function agendarServico(servicoId) {
            mostrarToast(`Agendamento do serviço ID: ${servicoId} será implementado em breve!`, 'warning');
        }

        // Smooth scroll para links da navbar
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

        // Fechar modais com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModal('modalLogin');
                fecharModal('modalRegistro');
            }
        });

        // Animação de entrada dos elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observar elementos para animação
        document.querySelectorAll('.service-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>