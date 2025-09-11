<?php
session_start();
header('Content-Type: application/json');

// Verificar se está logado e é admin
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['usuario_tipo'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

require_once '../models/Usuario.php';

try {
    $usuario = new Usuario();
    $metodo = $_SERVER['REQUEST_METHOD'];
    
    switch ($metodo) {
        case 'GET':
            // Listar todos os usuários
            $usuarios = $usuario->listarTodos();
            echo json_encode([
                'success' => true,
                'data' => $usuarios
            ]);
            break;
            
        case 'POST':
            $acao = $_POST['acao'] ?? '';
            
            switch ($acao) {
                case 'criar':
                    $dados = [
                        'nome' => $_POST['nome'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'telefone' => $_POST['telefone'] ?? '',
                        'senha' => $_POST['senha'] ?? '',
                        'tipo_usuario' => $_POST['tipo_usuario'] ?? 'cliente'
                    ];
                    
                    // Validações básicas
                    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha'])) {
                        throw new Exception('Nome, email e senha são obrigatórios');
                    }
                    
                    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Email inválido');
                    }
                    
                    if (strlen($dados['senha']) < 6) {
                        throw new Exception('A senha deve ter pelo menos 6 caracteres');
                    }
                    
                    $resultado = $usuario->registrar($dados);
                    echo json_encode($resultado);
                    break;
                    
                case 'atualizar':
                    $id = $_POST['id'] ?? '';
                    $dados = [
                        'nome' => $_POST['nome'] ?? '',
                        'telefone' => $_POST['telefone'] ?? '',
                        'tipo_usuario' => $_POST['tipo_usuario'] ?? 'cliente'
                    ];
                    
                    // Se uma nova senha foi fornecida
                    if (!empty($_POST['senha'])) {
                        $dados['senha'] = $_POST['senha'];
                    }
                    
                    if (empty($id) || empty($dados['nome'])) {
                        throw new Exception('ID e nome são obrigatórios');
                    }
                    
                    $resultado = $usuario->atualizarPerfil($id, $dados);
                    echo json_encode($resultado);
                    break;
                    
                case 'alterarStatus':
                    $id = $_POST['id'] ?? '';
                    $status = $_POST['status'] ?? '';
                    
                    if (empty($id) || empty($status)) {
                        throw new Exception('ID e status são obrigatórios');
                    }
                    
                    if (!in_array($status, ['ativo', 'inativo'])) {
                        throw new Exception('Status inválido');
                    }
                    
                    // Não permitir inativar o próprio usuário
                    if ($id == $_SESSION['usuario_id']) {
                        throw new Exception('Você não pode alterar seu próprio status');
                    }
                    
                    $resultado = $usuario->alterarStatus($id, $status);
                    echo json_encode($resultado);
                    break;
                    
                default:
                    throw new Exception('Ação não reconhecida');
            }
            break;
            
        default:
            throw new Exception('Método não permitido');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>