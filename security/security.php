<?php
session_start();

// Configurações de segurança
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 em HTTPS

// Função para gerar token CSRF
function gerarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Função para verificar token CSRF
function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Função para sanitizar entrada
function sanitizarEntrada($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Função para validar email
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para validar senha (mínimo 6 caracteres)
function validarSenha($senha) {
    return strlen($senha) >= 6;
}

// Função para verificar se usuário está logado
function verificarLogin() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Função para fazer logout
function logout() {
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
}

// Função para redirecionar
function redirecionar($url) {
    header("Location: $url");
    exit();
}

// Função para retornar resposta JSON
function retornarJSON($sucesso, $mensagem, $dados = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'sucesso' => $sucesso,
        'mensagem' => $mensagem,
        'dados' => $dados
    ]);
    exit();
}

// Função para limitar tentativas de login (rate limiting básico)
function verificarTentativasLogin($ip) {
    if (!isset($_SESSION['tentativas_login'])) {
        $_SESSION['tentativas_login'] = [];
    }
    
    $agora = time();
    $tentativas = &$_SESSION['tentativas_login'];
    
    // Remove tentativas antigas (mais de 15 minutos)
    foreach ($tentativas as $key => $tentativa) {
        if ($agora - $tentativa['tempo'] > 900) {
            unset($tentativas[$key]);
        }
    }
    
    // Conta tentativas do IP atual
    $count = 0;
    foreach ($tentativas as $tentativa) {
        if ($tentativa['ip'] === $ip) {
            $count++;
        }
    }
    
    return $count < 5; // Máximo 5 tentativas em 15 minutos
}

// Função para registrar tentativa de login
function registrarTentativaLogin($ip) {
    if (!isset($_SESSION['tentativas_login'])) {
        $_SESSION['tentativas_login'] = [];
    }
    
    $_SESSION['tentativas_login'][] = [
        'ip' => $ip,
        'tempo' => time()
    ];
}
?>