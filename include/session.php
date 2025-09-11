<?php
// Verificação de sessão para o sistema da barbearia
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função para verificar se usuário está logado
function verificarLogin() {
    if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
        header("Location: login.php");
        exit();
    }
}

// Função para verificar se é admin
function verificarAdmin() {
    verificarLogin();
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
        header("Location: dashboard.php");
        exit();
    }
}

// Função para fazer logout
function logout() {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Função para obter dados do usuário logado
function getUsuarioLogado() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nome' => $_SESSION['usuario_nome'] ?? null,
        'email' => $_SESSION['usuario_email'] ?? null,
        'tipo' => $_SESSION['tipo_usuario'] ?? null
    ];
}

// Função para definir mensagens de feedback
function setMensagem($mensagem, $tipo = 'info') {
    $_SESSION['mensagem'] = $mensagem;
    $_SESSION['tipo_mensagem'] = $tipo;
}

// Função para exibir mensagens
function getMensagem() {
    if (isset($_SESSION['mensagem'])) {
        $mensagem = $_SESSION['mensagem'];
        $tipo = $_SESSION['tipo_mensagem'] ?? 'info';
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
        return ['mensagem' => $mensagem, 'tipo' => $tipo];
    }
    return null;
}
?>