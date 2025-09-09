<?php
// api/register.php
session_start();

require_once '../config/database.php';
require_once '../security/Security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Método não permitido']);
    exit;
}

try {
    $security = new Security();
    $db = Database::getInstance();
    
    // Verificar rate limiting
    $ip = $security->getClientIP();
    if (!$security->checkRateLimit($ip)) {
        http_response_code(429);
        echo json_encode([
            'error' => true, 
            'message' => 'Muitas tentativas. Aguarde um momento.'
        ]);
        exit;
    }
    
    // Validar CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!$security->validateCSRFToken($csrfToken)) {
        http_response_code(403);
        echo json_encode(['error' => true, 'message' => 'Token de segurança inválido']);
        exit;
    }
    
    // Sanitizar e validar dados
    $name = $security->sanitizeInput($_POST['name'] ?? '', 'string');
    $email = $security->sanitizeInput($_POST['email'] ?? '', 'email');
    $phone = $security->sanitizeInput($_POST['phone'] ?? '', 'phone');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validações
    $errors = [];
    
    if (!$name || strlen($name) < 2) {
        $errors[] = 'Nome deve ter pelo menos 2 caracteres';
    }
    
    if (!$email || !$security->validateEmail($email)) {
        $errors[] = 'E-mail inválido';
    }
    
    if ($phone && !$security->validatePhone($phone)) {
        $errors[] = 'Telefone inválido';
    }
    
    if (!$password || strlen($password) < 6) {
        $errors[] = 'Senha deve ter pelo menos 6 caracteres';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Senhas não coincidem';
    }
    
    if (!empty($errors)) {
        echo json_encode(['error' => true, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Verificar se o e-mail já existe
    $stmt = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => true, 'message' => 'Este e-mail já está cadastrado']);
        exit;
    }
    
    // Usar transação para garantir consistência
    $userId = $db->transaction(function($db) use ($name, $email, $phone, $password, $security, $ip) {
        // Hash da senha
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        // Inserir usuário
        $stmt = $db->query("
            INSERT INTO users (name, email, phone, password, created_at, updated_at, is_active) 
            VALUES (?, ?, ?, ?, NOW(), NOW(), 1)
        ", [$name, $email, $phone, $passwordHash]);
        
        $userId = $db->lastInsertId();
        
        // Log de criação de conta
        $security->logSecurity($userId, 'account_created', $ip, [
            'email' => $email,
            'registration_method' => 'standard'
        ], 'info');
        
        return $userId;
    });
    
    echo json_encode([
        'success' => true,
        'message' => 'Conta criada com sucesso! Faça login para continuar.',
        'user_id' => $userId
    ]);
    
} catch (Exception $e) {
    error_log("Erro no registro: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor']);
}