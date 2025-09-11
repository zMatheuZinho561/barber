<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Barbearia Premium</title>
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
                        'fade-in-up': 'fadeInUp 0.5s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-in',
                        'slide-in': 'slideIn 0.4s ease-out'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <!-- Background Pattern -->
<div class="absolute inset-0 opacity-5">
    <div class="absolute inset-0" 
         style="background-image: url('data:image/svg+xml;utf8,<svg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'><g fill=\'none\' fill-rule=\'evenodd\'><g fill=\'white\' fill-opacity=\'0.1\'><circle cx=\'30\' cy=\'30\' r=\'1\'/></g></svg>');">
    </div>
</div>

    <!-- Navigation -->
    <nav class="relative z-10 p-6">
        <div class="flex items-center justify-between max-w-7xl mx-auto">
            <div class="flex items-center space-x-2 animate-slide-in">
                <div class="w-10 h-10 bg-gradient-to-r from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">✂</span>
                </div>
                <span class="text-white font-bold text-xl">Barbearia Premium</span>
            </div>
            <a href="index.php" class="text-gray-300 hover:text-white transition-colors duration-300 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Voltar ao início</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4 py-12">
        <div class="max-w-md w-full animate-fade-in-up">
            <!-- Card -->
            <div class="bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">Criar Conta</h1>
                    <p class="text-gray-300">Junte-se à nossa barbearia premium</p>
                </div>

                <!-- Alert Messages -->
                <div id="alertContainer" class="hidden mb-6">
                    <div id="alertMessage" class="p-4 rounded-xl text-sm font-medium"></div>
                </div>

                <!-- Form -->
                <form id="registerForm" class="space-y-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-300 mb-2">
                            Nome Completo *
                        </label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            required 
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                            placeholder="Seu nome completo"
                        >
                        <div class="text-red-400 text-sm mt-1 hidden" id="nome-error"></div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                            Email *
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                            placeholder="seu@email.com"
                        >
                        <div class="text-red-400 text-sm mt-1 hidden" id="email-error"></div>
                    </div>

                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-300 mb-2">
                            Telefone
                        </label>
                        <input 
                            type="tel" 
                            id="telefone" 
                            name="telefone"
                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                            placeholder="(11) 99999-9999"
                        >
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-300 mb-2">
                            Senha *
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="senha" 
                                name="senha" 
                                required 
                                minlength="6"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                                placeholder="Mínimo 6 caracteres"
                            >
                            <button type="button" onclick="togglePassword('senha')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="text-red-400 text-sm mt-1 hidden" id="senha-error"></div>
                    </div>

                    <div>
                        <label for="confirmar_senha" class="block text-sm font-medium text-gray-300 mb-2">
                            Confirmar Senha *
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirmar_senha" 
                                name="confirmar_senha" 
                                required 
                                minlength="6"
                                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300"
                                placeholder="Confirme sua senha"
                            >
                            <button type="button" onclick="togglePassword('confirmar_senha')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="text-red-400 text-sm mt-1 hidden" id="confirmar-senha-error"></div>
                    </div>

                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-semibold py-4 rounded-xl transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-transparent"
                    >
                        <span id="submitText">Criar Conta</span>
                        <span id="submitSpinner" class="hidden">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Criando conta...
                        </span>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="text-center mt-6 pt-6 border-t border-white/20">
                    <p class="text-gray-300">
                        Já tem uma conta? 
                        <a href="login.php" class="text-orange-400 hover:text-orange-300 font-medium transition-colors duration-300">
                            Faça login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length >= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 7) {
                value = value.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{2})(\d+)/, '($1) $2');
            }
            
            e.target.value = value;
        });

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const type = field.type === 'password' ? 'text' : 'password';
            field.type = type;
        }

        // Mostrar alerta
        function showAlert(message, type) {
            const container = document.getElementById('alertContainer');
            const messageEl = document.getElementById('alertMessage');
            
            container.classList.remove('hidden');
            messageEl.className = `p-4 rounded-xl text-sm font-medium ${
                type === 'error' 
                    ? 'bg-red-500/20 text-red-300 border border-red-500/30' 
                    : 'bg-green-500/20 text-green-300 border border-green-500/30'
            }`;
            messageEl.textContent = message;
            
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }

        // Validação do formulário
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            // Limpar erros anteriores
            document.querySelectorAll('[id$="-error"]').forEach(el => el.classList.add('hidden'));
            
            const formData = new FormData(this);
            
            // Validações client-side
            const nome = formData.get('nome').trim();
            const email = formData.get('email').trim();
            const senha = formData.get('senha');
            const confirmarSenha = formData.get('confirmar_senha');
            
            let hasError = false;
            
            if (nome.length < 2) {
                document.getElementById('nome-error').textContent = 'Nome deve ter pelo menos 2 caracteres';
                document.getElementById('nome-error').classList.remove('hidden');
                hasError = true;
            }
            
            if (!email.includes('@')) {
                document.getElementById('email-error').textContent = 'Email inválido';
                document.getElementById('email-error').classList.remove('hidden');
                hasError = true;
            }
            
            if (senha.length < 6) {
                document.getElementById('senha-error').textContent = 'Senha deve ter pelo menos 6 caracteres';
                document.getElementById('senha-error').classList.remove('hidden');
                hasError = true;
            }
            
            if (senha !== confirmarSenha) {
                document.getElementById('confirmar-senha-error').textContent = 'As senhas não coincidem';
                document.getElementById('confirmar-senha-error').classList.remove('hidden');
                hasError = true;
            }
            
            if (hasError) return;
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitText.classList.add('hidden');
            submitSpinner.classList.remove('hidden');
            
            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Conta criada com sucesso! Redirecionando para o login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showAlert(result.message || 'Erro ao criar conta. Tente novamente.', 'error');
                }
            } catch (error) {
                showAlert('Erro de conexão. Tente novamente.', 'error');
            } finally {
                // Remover loading
                submitBtn.disabled = false;
                submitText.classList.remove('hidden');
                submitSpinner.classList.add('hidden');
            }
        });

        // Animações de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.animate-fade-in-up, .animate-slide-in');
            elements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>