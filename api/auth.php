<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../models/Usuario.php';

try {
    $usuario = new Usuario();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }
    
    $acao = $_POST['acao'] ?? 'login';
    
    switch ($acao) {
        case 'login':
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($email) || empty($senha)) {
                throw new Exception('Email e senha são obrigatórios');
            }
            
            $resultado = $usuario->login($email, $senha);
            break;
            
        case 'registrar':
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            if (empty($nome) || empty($email) || empty($senha)) {
                throw new Exception('Nome, email e senha são obrigatórios');
            }
            
            if (strlen($senha) < 6) {
                throw new Exception('A senha deve ter pelo menos 6 caracteres');
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            $dados = [
                'nome' => trim($nome),
                'email' => trim($email),
                'telefone' => trim($telefone),
                'senha' => $senha
            ];
            
            $resultado = $usuario->registrar($dados);
            break;
            
        case 'logout':
            $resultado = $usuario->logout();
            break;
            
        default:
            throw new Exception('Ação não reconhecida');
    }
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>