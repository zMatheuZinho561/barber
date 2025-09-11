<?php

// api/login.php
require_once '../config/database.php';
require_once '../config/auth.php'; // ✅ necessário para usar setAuthToken()

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // 🔎 Validações básicas
    if (empty($email) || empty($senha)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email e senha são obrigatórios'
        ]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email inválido'
        ]);
        exit;
    }

    // 🔌 Conexão com banco
    $database = new Database();
    $conn = $database->getConnection();

    // Buscar usuário
    $query = "SELECT id, nome, email, senha, tipo, ativo FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Se não encontrou ou senha errada
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        error_log("Tentativa de login inválida para email: $email");
        echo json_encode([
            'success' => false,
            'message' => 'Email ou senha incorretos'
        ]);
        exit;
    }

    // Conta desativada
    if (!$usuario['ativo']) {
        echo json_encode([
            'success' => false,
            'message' => 'Conta desativada. Entre em contato com o suporte.'
        ]);
        exit;
    }

    // ✅ Dados do usuário
    $userData = [
        'id'   => $usuario['id'],
        'nome' => $usuario['nome'],
        'email'=> $usuario['email'],
        'tipo' => $usuario['tipo']
    ];

    // ✅ gera token JWT + salva sessão/cookie
    setAuthToken($userData);

    // Atualizar último login
    $query = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$usuario['id']]);

    // Redirecionamento conforme tipo
    $redirect = $usuario['tipo'] === 'admin'
        ? 'admin/dashboard.php'
        : 'perfil.php';

    echo json_encode([
        'success'   => true,
        'message'   => 'Login realizado com sucesso',
        'user_type' => $usuario['tipo'],
        'user_name' => $usuario['nome'],
        'redirect'  => $redirect
    ]);

} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
}