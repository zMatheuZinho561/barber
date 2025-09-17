<?php
// Só inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../include/auth.php';

$auth = new Auth();
$auth->logout();
?>