<?php
// api/register.php
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';
    
    // Validações server-side
    $errors = [];
    
    if (empty($nome) || strlen($nome) < 2) {
        $errors['nome'] = 'Nome deve ter pelo menos 2 caracteres';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido';
    }
    
    if (empty($senha) || strlen($senha) < 6) {
        $errors['senha'] = 'Senha deve ter pelo menos 6 caracteres';
    }
    
    if ($senha !== $confirmar_senha) {
        $errors['confirmar_senha'] = 'As senhas não coincidem';
    }
    
    // Validar telefone se fornecido
    if (!empty($telefone)) {
        $telefone_limpo = preg_replace('/\D/', '', $telefone);
        if (strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
            $errors['telefone'] = 'Telefone inválido';
        }
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Dados inválidos',
            'errors' => $errors
        ]);
        exit;
    }
    
    $database = new Database();
    $conn = $database->getConnection();
    
    // Verificar se email já existe
    $query = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Este email já está cadastrado'
        ]);
        exit;
    }
    
    // Criar usuário
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $query = "INSERT INTO usuarios (nome, email, telefone, senha, data_criacao) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$nome, $email, $telefone, $senha_hash])) {
        // Log de auditoria
        error_log("Novo usuário registrado: $email");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Usuário cadastrado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao criar usuário');
    }
    
} catch (Exception $e) {
    error_log("Erro no registro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro interno do servidor'
    ]);
}
?>