<?php
// api/usuarios.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../models/Usuario.php';

try {
    $acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
    
    if (empty($acao)) {
        throw new Exception('Ação não especificada');
    }
    
    $usuarioModel = new Usuario();
    
    switch ($acao) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                throw new Exception('Email e senha são obrigatórios');
            }
            
            $resultado = $usuarioModel->login($email, $senha);
            echo json_encode($resultado);
            break;
            
        case 'registro':
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($nome) || empty($email) || empty($senha)) {
                throw new Exception('Nome, email e senha são obrigatórios');
            }
            
            // Validar email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            // Validar senha
            if (strlen($senha) < 6) {
                throw new Exception('A senha deve ter pelo menos 6 caracteres');
            }
            
            $dadosUsuario = [
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'senha' => $senha,
                'tipo_usuario' => 'cliente'
            ];
            
            $resultado = $usuarioModel->registrar($dadosUsuario);
            
            // Se o registro foi bem-sucedido, fazer login automático
            if ($resultado['success']) {
                $loginResult = $usuarioModel->login($email, $senha);
                if ($loginResult['success']) {
                    $resultado['usuario'] = $loginResult['usuario'];
                }
            }
            
            echo json_encode($resultado);
            break;
            
        case 'logout':
            $resultado = $usuarioModel->logout();
            echo json_encode($resultado);
            break;
            
        case 'perfil':
            if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
                throw new Exception('Usuário não logado');
            }
            
            $usuario = $usuarioModel->obterPorId($_SESSION['usuario_id']);
            
            if ($usuario) {
                echo json_encode([
                    'success' => true,
                    'usuario' => [
                        'id' => $usuario['id'],
                        'nome' => $usuario['nome'],
                        'email' => $usuario['email'],
                        'telefone' => $usuario['telefone'],
                        'tipo_usuario' => $usuario['tipo_usuario']
                    ]
                ]);
            } else {
                throw new Exception('Usuário não encontrado');
            }
            break;
            
        case 'atualizar_perfil':
            if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
                throw new Exception('Usuário não logado');
            }
            
            $dados = [
                'nome' => $_POST['nome'] ?? '',
                'telefone' => $_POST['telefone'] ?? ''
            ];
            
            // Se uma nova senha foi fornecida
            if (!empty($_POST['nova_senha'])) {
                if (empty($_POST['senha_atual'])) {
                    throw new Exception('Informe a senha atual para alterar');
                }
                
                if (!$usuarioModel->verificarSenha($_SESSION['usuario_id'], $_POST['senha_atual'])) {
                    throw new Exception('Senha atual incorreta');
                }
                
                $dados['senha'] = $_POST['nova_senha'];
            }
            
            $resultado = $usuarioModel->atualizarPerfil($_SESSION['usuario_id'], $dados);
            echo json_encode($resultado);
            break;
            
        case 'verificar_email':
            $email = $_POST['email'] ?? '';
            $usuarioId = $_POST['usuario_id'] ?? null;
            
            if (empty($email)) {
                throw new Exception('Email é obrigatório');
            }
            
            $usuario = $usuarioModel->buscarPorEmail($email);
            
            // Se encontrou um usuário e não é o próprio usuário atual
            if ($usuario && (!$usuarioId || $usuario['id'] != $usuarioId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Este email já está em uso'
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Email disponível'
                ]);
            }
            break;
            
        default:
            throw new Exception('Ação não reconhecida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>