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
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    if ($senha !== $confirmar_senha) {
        $message = 'As senhas não coincidem!';
        $messageType = 'error';
    } else {
        $result = $auth->register($nome, $email, $telefone, $senha);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - BarberShop Pro</title>
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Criar Conta</h1>
            <p class="text-gray-600">Junte-se à família BarberShop Pro</p>
        </div>

        <!-- Mensagem de feedback -->
        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" class="space-y-6" id="registerForm">
            <!-- Nome -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    <i class="fas fa-user mr-2 text-indigo-600"></i>Nome Completo
                </label>
                <input type="text" name="nome" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                       placeholder="Digite seu nome completo"
                       value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
            </div>

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

            <!-- Telefone -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    <i class="fas fa-phone mr-2 text-indigo-600"></i>Telefone
                </label>
                <input type="text" name="telefone" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                       placeholder="(00) 00000-0000"
                       id="telefone"
                       value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
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

            <!-- Confirmar Senha -->
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    <i class="fas fa-lock mr-2 text-indigo-600"></i>Confirmar Senha
                </label>
                <div class="relative">
                    <input type="password" name="confirmar_senha" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all pr-12"
                           placeholder="Confirme sua senha"
                           id="confirmar_senha">
                    <button type="button" onclick="togglePassword('confirmar_senha')"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="confirmar_senha-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Botão de Cadastro -->
            <button type="submit" 
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all transform hover:scale-105">
                <i class="fas fa-user-plus mr-2"></i>
                Criar Conta
            </button>
        </form>

        <!-- Links -->
        <div class="mt-8 text-center space-y-4">
            <p class="text-gray-600">
                Já tem uma conta? 
                <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                    Fazer Login
                </a>
            </p>
            <a href="index.php" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Voltar ao início
            </a>
        </div>
    </div>

    <script>
        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            
            if (value.length > 0) {
                formattedValue = '(' + value.substring(0, 2);
            }
            if (value.length > 2) {
                formattedValue += ') ' + value.substring(2, 7);
            }
            if (value.length > 7) {
                formattedValue += '-' + value.substring(7, 11);
            }
            
            e.target.value = formattedValue;
        });

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

        // Validação de senha em tempo real
        document.getElementById('confirmar_senha').addEventListener('input', function() {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = this.value;
            
            if (confirmarSenha && senha !== confirmarSenha) {
                this.classList.add('border-red-500');
                this.classList.remove('border-gray-300');
            } else {
                this.classList.remove('border-red-500');
                this.classList.add('border-gray-300');
            }
        });

        // Validação do formulário
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (senha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }
            
            if (senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter pelo menos 6 caracteres!');
                return false;
            }
        });
    </script>
</body>
</html>