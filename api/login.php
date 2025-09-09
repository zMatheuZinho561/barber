<?php
// api/login.php
session_start();

require_once '../config/database.php';
require_once '../security/Security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
    
    // Verificar força bruta
    $email = $security->sanitizeInput($_POST['email'] ?? '', 'email');
    $bruteForceCheck = $security->checkBruteForce($ip, $email);
    
    if (!$bruteForceCheck['allowed']) {
        http_response_code(429);
        echo json_encode([
            'error' => true,
            'message' => $bruteForceCheck['message'],
            'blocked' => true
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
    $email = $security->sanitizeInput($_POST['email'] ?? '', 'email');
    $password = $_POST['password'] ?? '';
    
    if (!$email || !$security->validateEmail($email)) {
        echo json_encode(['error' => true, 'message' => 'E-mail inválido']);
        exit;
    }
    
    if (!$password) {
        echo json_encode(['error' => true, 'message' => 'Senha é obrigatória']);
        exit;
    }
    
    // Buscar usuário no banco
    $stmt = $db->query("
        SELECT id, email, password, name, is_active, created_at
        FROM users 
        WHERE email = ? AND is_active = 1
    ", [$email]);
    
    $user = $stmt->fetch();
    
    if (!$user) {
        // Registrar tentativa de login falhada
        $security->recordFailedLogin($ip, $email);
        echo json_encode(['error' => true, 'message' => 'E-mail ou senha incorretos']);
        exit;
    }
    
    // Verificar senha
    if (!password_verify($password, $user['password'])) {
        $security->recordFailedLogin($ip, $email);
        echo json_encode(['error' => true, 'message' => 'E-mail ou senha incorretos']);
        exit;
    }
    
    // Login bem-sucedido
    $sessionId = $security->createSession($user['id'], $ip);
    $security->recordSuccessfulLogin($user['id'], $ip, $email);
    
    // Preparar dados do usuário para a sessão
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['name']
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ],
        'redirect' => '/dashboard.php'
    ]);
    
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor']);
}