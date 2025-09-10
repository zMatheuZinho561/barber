<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barbearia Elite - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2c2c2c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="%23ffffff" opacity="0.05"/><circle cx="80" cy="40" r="0.5" fill="%23ffffff" opacity="0.03"/><circle cx="40" cy="80" r="0.8" fill="%23ffffff" opacity="0.04"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #d4af37, #ffd700, #d4af37);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 3rem;
            color: #d4af37;
            margin-bottom: 10px;
        }

        .logo h1 {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .logo p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-container {
            position: relative;
        }

        .form {
            display: none;
        }

        .form.active {
            display: block;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #d4af37;
            font-size: 1.1rem;
            z-index: 2;
        }

        .form-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            transform: translateY(-2px);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%);
            border: none;
            border-radius: 12px;
            color: #1a1a1a;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #1a1a1a;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .toggle-form {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toggle-form p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
        }

        .toggle-btn {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .toggle-btn:hover {
            color: #ffd700;
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
        }

        .alert.show {
            opacity: 1;
            transform: translateY(0);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ef4444;
        }

        .form-title {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: 300;
            text-align: center;
            margin-bottom: 25px;
            position: relative;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #d4af37, #ffd700);
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .form-group input {
                padding: 12px 12px 12px 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-cut"></i>
            <h1>BARBEARIA ELITE</h1>
            <p>Estilo e tradição desde sempre</p>
        </div>

        <div id="alert-container"></div>

        <div class="form-container">
            <!-- Formulário de Login -->
            <form id="loginForm" class="form active">
                <h2 class="form-title">Entrar</h2>
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="loginEmail" placeholder="Seu email" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="loginSenha" placeholder="Sua senha" required>
                </div>
                <button type="submit" class="btn" id="loginBtn">
                    <span class="btn-text">Entrar</span>
                </button>
                <div class="toggle-form">
                    <p>Ainda não tem conta?</p>
                    <a class="toggle-btn" onclick="toggleForm('register')">Criar conta agora</a>
                </div>
            </form>

            <!-- Formulário de Registro -->
            <form id="registerForm" class="form">
                <h2 class="form-title">Criar Conta</h2>
                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="registerNome" placeholder="Seu nome completo" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="registerEmail" placeholder="Seu email" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="registerTelefone" placeholder="(11) 99999-9999">
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="registerSenha" placeholder="Sua senha (mín. 6 caracteres)" required>
                </div>
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="registerConfirmarSenha" placeholder="Confirme sua senha" required>
                </div>
                <button type="submit" class="btn" id="registerBtn">
                    <span class="btn-text">Criar Conta</span>
                </button>
                <div class="toggle-form">
                    <p>Já tem uma conta?</p>
                    <a class="toggle-btn" onclick="toggleForm('login')">Fazer login</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Token CSRF (será definido via PHP)
        const csrfToken = '<?php require_once "../security/security.php"; echo gerarTokenCSRF(); ?>';

        // Função para alternar entre formulários
        function toggleForm(form) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            
            if (form === 'register') {
                loginForm.classList.remove('active');
                registerForm.classList.add('active');
            } else {
                registerForm.classList.remove('active');
                loginForm.classList.add('active');
            }
            
            // Limpar alertas
            clearAlerts();
        }

        // Função para mostrar alerta
        function showAlert(message, type = 'error') {
            const alertContainer = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);
            
            setTimeout(() => alert.classList.add('show'), 100);
            
            if (type === 'success') {
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                }, 3000);
            }
        }

        // Função para limpar alertas
        function clearAlerts() {
            document.getElementById('alert-container').innerHTML = '';
        }

        // Função para definir estado de loading do botão
        function setButtonLoading(button, loading) {
            const btnText = button.querySelector('.btn-text');
            
            if (loading) {
                button.disabled = true;
                button.classList.add('btn-loading');
                btnText.style.opacity = '0';
            } else {
                button.disabled = false;
                button.classList.remove('btn-loading');
                btnText.style.opacity = '1';
            }
        }

        // Máscara para telefone
        function maskPhone(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
            input.value = value;
        }

        document.getElementById('registerTelefone').addEventListener('input', function() {
            maskPhone(this);
        });

        // Submissão do formulário de login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value.trim();
            const senha = document.getElementById('loginSenha').value;
            const button = document.getElementById('loginBtn');
            
            if (!email || !senha) {
                showAlert('Por favor, preencha todos os campos.');
                return;
            }
            
            clearAlerts();
            setButtonLoading(button, true);
            
            $.ajax({
                url: 'ajax/auth.php',
                type: 'POST',
                data: {
                    acao: 'login',
                    email: email,
                    senha: senha,
                    csrf_token: csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    setButtonLoading(button, false);
                    
                    if (response.sucesso) {
                        showAlert(response.mensagem, 'success');
                        setTimeout(() => {
                            window.location.href = response.dados.redirect || 'dashboard.php';
                        }, 1500);
                    } else {
                        showAlert(response.mensagem);
                    }
                },
                error: function() {
                    setButtonLoading(button, false);
                    showAlert('Erro de conexão. Tente novamente.');
                }
            });
        });

        // Submissão do formulário de registro
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nome = document.getElementById('registerNome').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const telefone = document.getElementById('registerTelefone').value.trim();
            const senha = document.getElementById('registerSenha').value;
            const confirmarSenha = document.getElementById('registerConfirmarSenha').value;
            const button = document.getElementById('registerBtn');
            
            if (!nome || !email || !senha || !confirmarSenha) {
                showAlert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
            
            if (senha !== confirmarSenha) {
                showAlert('As senhas não coincidem.');
                return;
            }
            
            if (senha.length < 6) {
                showAlert('A senha deve ter pelo menos 6 caracteres.');
                return;
            }
            
            clearAlerts();
            setButtonLoading(button, true);
            
            $.ajax({
                url: 'ajax/auth.php',
                type: 'POST',
                data: {
                    acao: 'registro',
                    nome: nome,
                    email: email,
                    telefone: telefone,
                    senha: senha,
                    confirmar_senha: confirmarSenha,
                    csrf_token: csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    setButtonLoading(button, false);
                    
                    if (response.sucesso) {
                        showAlert(response.mensagem, 'success');
                        setTimeout(() => {
                            toggleForm('login');
                            document.getElementById('registerForm').reset();
                        }, 2000);
                    } else {
                        showAlert(response.mensagem);
                    }
                },
                error: function() {
                    setButtonLoading(button, false);
                    showAlert('Erro de conexão. Tente novamente.');
                }
            });
        });

        // Verificar se já está logado
        $(document).ready(function() {
            // Efeito de entrada suave
            $('.container').css({
                'opacity': '0',
                'transform': 'translateY(50px)'
            }).animate({
                'opacity': '1',
                'transform': 'translateY(0)'
            }, 800);
        });
    </script>
</body>
</html>