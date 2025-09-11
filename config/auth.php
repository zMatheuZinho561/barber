<?php
// config/auth.php
require_once __DIR__ . '/jwt.php';

function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function setAuthToken(array $user_data) {
    iniciarSessao();

    $jwt = new JWTHandler();
    $token = $jwt->generateToken($user_data);

    // cookie HttpOnly, SameSite=Lax
    setcookie('auth_token', $token, [
        'expires' => time() + 86400,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    $_SESSION = [
        'usuario_id'   => $user_data['id'],
        'nome'         => $user_data['nome'],
        'email'        => $user_data['email'],
        'tipo'         => $user_data['tipo'],
        'authenticated'=> true
    ];
}

function getAuthenticatedUser() {
    iniciarSessao();

    if (isset($_COOKIE['auth_token'])) {
        $jwt = new JWTHandler();
        $data = $jwt->validateToken($_COOKIE['auth_token']);
        if ($data) return $data;
    }

    return $_SESSION['authenticated'] ?? false ? [
        'user_id' => $_SESSION['usuario_id'],
        'nome'    => $_SESSION['nome'],
        'email'   => $_SESSION['email'],
        'tipo'    => $_SESSION['tipo']
    ] : false;
}

function usuarioLogado(): bool {
    return getAuthenticatedUser() !== false;
}

function isAdmin(): bool {
    $user = getAuthenticatedUser();
    return $user && $user['tipo'] === 'admin';
}

function requireAuth() {
    if (!usuarioLogado()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Não autorizado']);
        exit;
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }
}

function logout() {
    iniciarSessao();
    setcookie('auth_token', '', time() - 3600, '/');
    session_destroy();
}