<?php
// api/logout.php
session_start();

require_once '../security/Security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $security = new Security();
    
    // Obter sessão atual
    $sessionId = $_COOKIE['barbershop_session'] ?? null;
    
    if ($sessionId) {
        // Destruir sessão no banco de dados
        $security->destroySession($sessionId);
        
        // Log de logout
        if (isset($_SESSION['user'])) {
            $security->logSecurity($_SESSION['user']['id'], 'logout', $security->getClientIP(), [
                'session_destroyed' => true
            ], 'info');
        }
    }
    
    // Limpar dados da sessão PHP
    session_unset();
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Logout realizado com sucesso',
        'redirect' => '/login.php'
    ]);
    
} catch (Exception $e) {
    error_log("Erro no logout: " . $e->getMessage());
    echo json_encode([
        'success' => true, // Sempre retornar sucesso no logout
        'message' => 'Logout realizado',
        'redirect' => '/login.php'
    ]);
}