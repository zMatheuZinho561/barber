<?php
// api/update_profile.php
require_once '../config/database.php';

header('Content-Type: application/json');
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    $user = getAuthenticatedUser();
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    
    // Validações
    $errors = [];
    
    if (empty($nome) || strlen($nome) < 2) {
        $errors['nome'] = 'Nome deve ter pelo menos 2 caracteres';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email inválido';
    }
    
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
    
    // Verificar se email já existe (exceto para o usuário atual)
    $query = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email, $user['user_id']]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Este email já está sendo usado por outro usuário'
        ]);
        exit;
    }
    
    // Atualizar dados do usuário
    $query = "UPDATE usuarios SET nome = ?, email = ?, telefone = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$nome, $email, $telefone, $user['user_id']])) {
        // Atualizar dados na sessão/token
        $updatedUserData = [
            'id' => $user['user_id'],
            'nome' => $nome,
            'email' => $email,
            'tipo' => $user['tipo']
        ];
        setAuthToken($updatedUserData);
        
        echo json_encode([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso'
        ]);
    } else {
        throw new Exception('Erro ao atualizar perfil');
    }
    
} catch (Exception $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}
?>