<?php
// api/google-auth.php
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
    
    // Ler dados JSON
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    if (!$jsonData) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Dados inválidos']);
        exit;
    }
    
    // Validar CSRF token
    $csrfToken = $jsonData['csrf_token'] ?? '';
    if (!$security->validateCSRFToken($csrfToken)) {
        http_response_code(403);
        echo json_encode(['error' => true, 'message' => 'Token de segurança inválido']);
        exit;
    }
    
    // Validar dados do Google
    $googleId = $security->sanitizeInput($jsonData['google_id'] ?? '', 'string');
    $email = $security->sanitizeInput($jsonData['email'] ?? '', 'email');
    $name = $security->sanitizeInput($jsonData['name'] ?? '', 'string');
    $avatar = $security->sanitizeInput($jsonData['avatar'] ?? '', 'string');
    
    if (!$googleId || !$email || !$name) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'Dados do Google incompletos']);
        exit;
    }
    
    if (!$security->validateEmail($email)) {
        http_response_code(400);
        echo json_encode(['error' => true, 'message' => 'E-mail inválido']);
        exit;
    }
    
    // TODO: Verificar token do Google com a API do Google
    // $googleTokenValid = verifyGoogleToken($jsonData['id_token']);
    // if (!$googleTokenValid) {
    //     echo json_encode(['error' => true, 'message' => 'Token do Google inválido']);
    //     exit;
    // }
    
    // Verificar se usuário já existe
    $stmt = $db->query("
        SELECT id, email, name, is_active, google_id, avatar
        FROM users 
        WHERE email = ? OR google_id = ?
    ", [$email, $googleId]);
    
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        // Usuário já existe
        if (!$existingUser['is_active']) {
            echo json_encode(['error' => true, 'message' => 'Conta desativada']);
            exit;
        }
        
        // Se não tem google_id, atualizar
        if (!$existingUser['google_id']) {
            $db->query("
                UPDATE users 
                SET google_id = ?, avatar = ?, updated_at = NOW()
                WHERE id = ?
            ", [$googleId, $avatar, $existingUser['id']]);
        }
        
        $userId = $existingUser['id'];
        $isNewUser = false;
        
    } else {
        // Criar novo usuário
        $userId = $db->transaction(function($db) use ($name, $email, $googleId, $avatar, $security, $ip) {
            $stmt = $db->query("
                INSERT INTO users (name, email, google_id, avatar, created_at, updated_at, is_active) 
                VALUES (?, ?, ?, ?, NOW(), NOW(), 1)
            ", [$name, $email, $googleId, $avatar]);
            
            $userId = $db->lastInsertId();
            
            // Log de criação de conta
            $security->logSecurity($userId, 'account_created', $ip, [
                'email' => $email,
                'registration_method' => 'google',
                'google_id' => $googleId
            ], 'info');
            
            return $userId;
        });
        
        $isNewUser = true;
    }
    
    // Criar sessão
    $sessionId = $security->createSession($userId, $ip);
    $security->recordSuccessfulLogin($userId, $ip, $email);
    
    // Preparar dados do usuário para a sessão
    $_SESSION['user'] = [
        'id' => $userId,
        'email' => $email,
        'name' => $name
    ];
    
    echo json_encode([
        'success' => true,
        'message' => $isNewUser ? 'Conta criada com sucesso!' : 'Login realizado com sucesso!',
        'user' => [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar
        ],
        'is_new_user' => $isNewUser,
        'redirect' => '/dashboard.php'
    ]);
    
} catch (Exception $e) {
    error_log("Erro no Google Auth: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Erro interno do servidor']);
}

// Função para verificar token do Google (implementar quando configurar OAuth)
function verifyGoogleToken($idToken) {
    // Implementar verificação real com Google
    // $client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);
    // $payload = $client->verifyIdToken($idToken);
    // return $payload !== false;
    
    return true; // Temporário
}