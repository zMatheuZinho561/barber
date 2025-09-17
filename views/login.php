<?php
require_once '../include/auth.php';

$auth = new Auth();
$message = '';
$messageType = '';

// Se já estiver logado, redireciona
if ($auth->isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    $result = $auth->login($email, $senha);
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
    
    if ($result['success']) {
        header("Location: /newbarber/index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BarberShop Pro</title>
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
    </style>
</head>
<body class="hero-gradient min-h-screen flex items-center justify-center p-4">
    <div class="glass-effect rounded-2xl shadow-2xl w-full max-w-md p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-indigo-600 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
                <i class="fas fa-cut text-2xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Bem-vindo de volta!</h1>
            <p class="text-gray-600">Entre em sua conta para agendar</p>
        </div>

        <!-- Mensagem de feedback -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" class="space-y-6" id="loginForm">
            <!-- Email -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    <i class="fas fa-envelope mr-2 text-indigo-600"></i>E-mail
                </label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                       placeholder="Digite seu e-mail"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <!-- Senha -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Senha
                </label>
                <div class="relative">
                    <input type="password" name="senha" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all pr-12"
                           placeholder="Digite sua senha"
                           id="senha">
                    <button type="button" onclick="togglePassword('senha')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="senha-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Botão de Login -->
            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Entrar
            </button>
        </form>

        <!-- Credenciais de teste -->
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">Credenciais de Teste:</h3>
            <div class="text-sm text-blue-700">
                <p><strong>Admin:</strong> admin@barbeararia.com / admin123</p>
                <p><strong>Cliente:</strong> joao@email.com / cliente123</p>
            </div>
        </div>

        <!-- Links -->
        <div class="mt-8 text-center space-y-4">
            <p class="text-gray-600">
                Não tem uma conta? 
                <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                    Cadastre-se
                </a>
            </p>
            <a href="index.php" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Voltar ao início
            </a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-fill para facilitar testes
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const testUser = urlParams.get('test');
            
            if (testUser === 'admin') {
                document.querySelector('input[name="email"]').value = 'admin@barbeararia.com';
                document.querySelector('input[name="senha"]').value = 'admin123';
            } else if (testUser === 'client') {
                document.querySelector('input[name="email"]').value = 'joao@email.com';
                document.querySelector('input[name="senha"]').value = 'cliente123';
            }
        });
    </script>
</body>
</html>