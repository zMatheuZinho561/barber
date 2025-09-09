<?php
// security/Security.php
require_once __DIR__ . '/../config/database.php';

class Security {
    private $db;
    private $config;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->loadConfig();
    }
    
    private function loadConfig() {
        $this->config = [
            'max_login_attempts' => 5,
            'block_duration' => 1800, // 30 minutos
            'session_timeout' => 3600, // 1 hora
            'strong_password' => false, // Para Google OAuth não precisamos
            'rate_limit_requests' => 100, // Requests por minuto
            'csrf_token_lifetime' => 3600
        ];
    }
    
    // ============================================
    // PROTEÇÃO CONTRA FORÇA BRUTA
    // ============================================
    
    public function checkBruteForce($ip, $email = null) {
        try {
            $stmt = $this->db->prepare("
                SELECT attempts, blocked_until, updated_at
                FROM login_attempts 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
                ORDER BY updated_at DESC LIMIT 1
            ");
            $stmt->execute([$ip, $email]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return ['allowed' => true, 'attempts' => 0];
            }
            
            // Verificar se ainda está bloqueado
            if ($result['blocked_until'] && strtotime($result['blocked_until']) > time()) {
                $this->logSecurity(null, 'blocked_login_attempt', $ip, [
                    'email' => $email,
                    'attempts' => $result['attempts'],
                    'blocked_until' => $result['blocked_until']
                ], 'warning');
                
                return [
                    'allowed' => false, 
                    'attempts' => $result['attempts'],
                    'blocked_until' => $result['blocked_until'],
                    'message' => 'IP bloqueado por muitas tentativas. Tente novamente em ' . 
                               $this->getTimeRemaining($result['blocked_until'])
                ];
            }
            
            // Se passou do tempo de bloqueio, resetar
            if ($result['blocked_until'] && strtotime($result['blocked_until']) <= time()) {
                $this->resetLoginAttempts($ip, $email);
                return ['allowed' => true, 'attempts' => 0];
            }
            
            // Verificar número de tentativas
            if ($result['attempts'] >= $this->config['max_login_attempts']) {
                $this->blockIP($ip, $email);
                return [
                    'allowed' => false,
                    'attempts' => $result['attempts'],
                    'message' => 'Muitas tentativas de login. IP bloqueado temporariamente.'
                ];
            }
            
            return ['allowed' => true, 'attempts' => $result['attempts']];
            
        } catch (Exception $e) {
            error_log("Erro no checkBruteForce: " . $e->getMessage());
            return ['allowed' => true, 'attempts' => 0]; // Em caso de erro, permitir
        }
    }
    
    public function recordFailedLogin($ip, $email = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO login_attempts (ip_address, email, attempts) 
                VALUES (?, ?, 1)
                ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1, 
                updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$ip, $email]);
            
            $this->logSecurity(null, 'failed_login', $ip, [
                'email' => $email,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ], 'warning');
            
        } catch (Exception $e) {
            error_log("Erro ao registrar tentativa de login: " . $e->getMessage());
        }
    }
    
    public function recordSuccessfulLogin($userId, $ip, $email) {
        try {
            // Resetar tentativas de login
            $this->resetLoginAttempts($ip, $email);
            
            // Atualizar último login do usuário
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Registrar login bem-sucedido
            $this->logSecurity($userId, 'successful_login', $ip, [
                'email' => $email,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ], 'info');
            
        } catch (Exception $e) {
            error_log("Erro ao registrar login bem-sucedido: " . $e->getMessage());
        }
    }
    
    private function blockIP($ip, $email = null) {
        try {
            $blockedUntil = date('Y-m-d H:i:s', time() + $this->config['block_duration']);
            
            $stmt = $this->db->prepare("
                UPDATE login_attempts 
                SET blocked_until = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
            ");
            $stmt->execute([$blockedUntil, $ip, $email]);
            
            $this->logSecurity(null, 'ip_blocked', $ip, [
                'email' => $email,
                'blocked_until' => $blockedUntil,
                'reason' => 'Excesso de tentativas de login'
            ], 'critical');
            
        } catch (Exception $e) {
            error_log("Erro ao bloquear IP: " . $e->getMessage());
        }
    }
    
    private function resetLoginAttempts($ip, $email = null) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM login_attempts 
                WHERE ip_address = ? AND (email = ? OR email IS NULL)
            ");
            $stmt->execute([$ip, $email]);
        } catch (Exception $e) {
            error_log("Erro ao resetar tentativas: " . $e->getMessage());
        }
    }
    
    // ============================================
    // GERENCIAMENTO DE SESSÕES
    // ============================================
    
    public function createSession($userId, $ip) {
        try {
            $sessionId = $this->generateSecureToken(32);
            $expiresAt = date('Y-m-d H:i:s', time() + $this->config['session_timeout']);
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Invalidar sessões antigas do usuário (limitar a 3 sessões ativas)
            $this->invalidateOldSessions($userId, 3);
            
            // Criar nova sessão
            $stmt = $this->db->prepare("
                INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, expires_at, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([$userId, $sessionId, $ip, $userAgent, $expiresAt]);
            
            // Definir cookie de sessão com configurações de segurança
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            setcookie('barbershop_session', $sessionId, [
                'expires' => time() + $this->config['session_timeout'],
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
            $this->logSecurity($userId, 'session_created', $ip, [
                'session_id' => substr($sessionId, 0, 8) . '...',
                'expires_at' => $expiresAt
            ], 'info');
            
            return $sessionId;
            
        } catch (Exception $e) {
            error_log("Erro ao criar sessão: " . $e->getMessage());
            throw new Exception('Erro ao criar sessão');
        }
    }
    
    public function validateSession($sessionId, $ip) {
        try {
            $stmt = $this->db->prepare("
                SELECT us.*, u.id as user_id, u.email, u.name, u.is_active
                FROM user_sessions us
                JOIN users u ON us.user_id = u.id
                WHERE us.session_id = ? AND us.is_active = 1 AND us.expires_at > NOW()
            ");
            $stmt->execute([$sessionId]);
            $session = $stmt->fetch();
            
            if (!$session) {
                return false;
            }
            
            // Verificar se usuário ainda está ativo
            if (!$session['is_active']) {
                $this->destroySession($sessionId);
                return false;
            }
            
            // Atualizar timestamp da sessão
            $this->updateSessionActivity($sessionId);
            
            // Renovar sessão se está próxima do vencimento (últimos 15 minutos)
            if (strtotime($session['expires_at']) - time() < 900) {
                $this->renewSession($sessionId);
            }
            
            return [
                'user_id' => $session['user_id'],
                'email' => $session['email'],
                'name' => $session['name']
            ];
            
        } catch (Exception $e) {
            error_log("Erro ao validar sessão: " . $e->getMessage());
            return false;
        }
    }
    
    public function destroySession($sessionId) {
        try {
            $stmt = $this->db->prepare("UPDATE user_sessions SET is_active = 0 WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            
            // Remover cookie
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            setcookie('barbershop_session', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            
        } catch (Exception $e) {
            error_log("Erro ao destruir sessão: " . $e->getMessage());
        }
    }
    
    private function invalidateOldSessions($userId, $maxSessions = 3) {
        try {
            // Manter apenas as N sessões mais recentes
            $stmt = $this->db->prepare("
                UPDATE user_sessions 
                SET is_active = 0 
                WHERE user_id = ? AND is_active = 1 
                AND id NOT IN (
                    SELECT id FROM (
                        SELECT id FROM user_sessions 
                        WHERE user_id = ? AND is_active = 1 
                        ORDER BY updated_at DESC 
                        LIMIT ?
                    ) AS recent_sessions
                )
            ");
            $stmt->execute([$userId, $userId, $maxSessions - 1]);
        } catch (Exception $e) {
            error_log("Erro ao invalidar sessões antigas: " . $e->getMessage());
        }
    }
    
    private function updateSessionActivity($sessionId) {
        try {
            $stmt = $this->db->prepare("UPDATE user_sessions SET updated_at = NOW() WHERE session_id = ?");
            $stmt->execute([$sessionId]);
        } catch (Exception $e) {
            error_log("Erro ao atualizar atividade da sessão: " . $e->getMessage());
        }
    }
    
    private function renewSession($sessionId) {
        try {
            $newExpiry = date('Y-m-d H:i:s', time() + $this->config['session_timeout']);
            $stmt = $this->db->prepare("UPDATE user_sessions SET expires_at = ? WHERE session_id = ?");
            $stmt->execute([$newExpiry, $sessionId]);
            
            // Atualizar cookie
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            setcookie('barbershop_session', $sessionId, [
                'expires' => time() + $this->config['session_timeout'],
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
                     
        } catch (Exception $e) {
            error_log("Erro ao renovar sessão: " . $e->getMessage());
        }
    }
    
    // ============================================
    // PROTEÇÃO CSRF
    // ============================================
    
    public function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) ||
            time() - $_SESSION['csrf_token_time'] > $this->config['csrf_token_lifetime']) {
            
            $_SESSION['csrf_token'] = $this->generateSecureToken(32);
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public function validateCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }
        
        if (time() - $_SESSION['csrf_token_time'] > $this->config['csrf_token_lifetime']) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // ============================================
    // SANITIZAÇÃO E VALIDAÇÃO
    // ============================================
    
    public function sanitizeInput($input, $type = 'string') {
        if ($input === null) return null;
        
        switch ($type) {
            case 'email':
                return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
                
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
                
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                
            case 'phone':
                return preg_replace('/[^0-9+()-\s]/', '', $input);
                
            case 'html':
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
                
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function validatePhone($phone) {
        // Formato brasileiro: (11) 99999-9999 ou 11999999999
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^[1-9]{2}9?[0-9]{8}$/', $phone);
    }
    
    public function validatePassword($password) {
        return strlen($password) >= 6;
    }
    
    // ============================================
    // RATE LIMITING
    // ============================================
    
    public function checkRateLimit($identifier, $maxRequests = null, $timeWindow = 60) {
        $maxRequests = $maxRequests ?? $this->config['rate_limit_requests'];
        
        try {
            $cacheDir = sys_get_temp_dir() . '/barbershop_rate_limit';
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }
            
            $key = "rate_limit_" . md5($identifier);
            $cacheFile = $cacheDir . "/$key.cache";
            $currentTime = time();
            $windowStart = $currentTime - $timeWindow;
            
            $requests = [];
            if (file_exists($cacheFile)) {
                $data = json_decode(file_get_contents($cacheFile), true);
                if ($data) {
                    $requests = array_filter($data, function($time) use ($windowStart) {
                        return $time > $windowStart;
                    });
                }
            }
            
            if (count($requests) >= $maxRequests) {
                $this->logSecurity(null, 'rate_limit_exceeded', $this->getClientIP(), [
                    'identifier' => $identifier,
                    'requests_count' => count($requests),
                    'max_allowed' => $maxRequests
                ], 'warning');
                
                return false;
            }
            
            // Adicionar request atual
            $requests[] = $currentTime;
            file_put_contents($cacheFile, json_encode($requests));
            
            return true;
            
        } catch (Exception $e) {
            error_log("Erro no rate limiting: " . $e->getMessage());
            return true; // Em caso de erro, permitir
        }
    }
    
    // ============================================
    // LOGGING DE SEGURANÇA
    // ============================================
    
    public function logSecurity($userId, $action, $ip, $details = [], $severity = 'info') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO security_logs (user_id, action, ip_address, user_agent, details, severity, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $action,
                $ip,
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                json_encode($details),
                $severity
            ]);
            
            // Log crítico também no arquivo de erro
            if ($severity === 'critical') {
                error_log("SEGURANÇA CRÍTICA: $action - IP: $ip - Detalhes: " . json_encode($details));
            }
            
        } catch (Exception $e) {
            error_log("Erro ao fazer log de segurança: " . $e->getMessage());
        }
    }
    
    // ============================================
    // UTILITÁRIOS
    // ============================================
    
    public function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                $ip = trim($ip);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    public function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    private function getTimeRemaining($timestamp) {
        $remaining = strtotime($timestamp) - time();
        
        if ($remaining <= 0) return 'agora';
        
        if ($remaining < 60) return $remaining . ' segundos';
        if ($remaining < 3600) return ceil($remaining / 60) . ' minutos';
        
        return ceil($remaining / 3600) . ' horas';
    }
    
    // ============================================
    // LIMPEZA AUTOMÁTICA
    // ============================================
    
    public function cleanup() {
        try {
            // Limpar sessões expiradas
            $this->db->prepare("DELETE FROM user_sessions WHERE expires_at < NOW() OR is_active = 0")->execute();
            
            // Limpar tentativas de login antigas (maiores que 24h)
            $this->db->prepare("DELETE FROM login_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)")->execute();
            
            // Limpar logs antigos (manter apenas logs críticos por mais tempo)
            $this->db->prepare("
                DELETE FROM security_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY) 
                AND severity IN ('info', 'warning')
            ")->execute();
            
            $this->db->prepare("
                DELETE FROM security_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) 
                AND severity IN ('error', 'critical')
            ")->execute();
            
        } catch (Exception $e) {
            error_log("Erro na limpeza de segurança: " . $e->getMessage());
        }
    }
}

// ============================================
// MIDDLEWARE DE SEGURANÇA
// ============================================

class SecurityMiddleware {
    private $security;
    
    public function __construct() {
        $this->security = new Security();
    }
    
    public function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $sessionId = $_COOKIE['barbershop_session'] ?? null;
        
        if (!$sessionId) {
            return $this->unauthorized();
        }
        
        $sessionData = $this->security->validateSession($sessionId, $this->security->getClientIP());
        
        if (!$sessionData) {
            return $this->unauthorized();
        }
        
        // Disponibilizar dados do usuário
        $_SESSION['user'] = $sessionData;
        return true;
    }
    
    public function checkBruteForce() {
        $ip = $this->security->getClientIP();
        $check = $this->security->checkBruteForce($ip);
        
        if (!$check['allowed']) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => $check['message'] ?? 'Muitas tentativas. Tente novamente mais tarde.'
            ]);
            exit;
        }
    }
    
    public function checkRateLimit($identifier = null) {
        $identifier = $identifier ?? $this->security->getClientIP();
        
        if (!$this->security->checkRateLimit($identifier)) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode([
                'error' => true,
                'message' => 'Muitas requisições. Aguarde um momento.'
            ]);
            exit;
        }
    }
    
    public function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            
            if (!$token || !$this->security->validateCSRFToken($token)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => true,
                    'message' => 'Token de segurança inválido'
                ]);
                exit;
            }
        }
    }
    
    private function unauthorized() {
        if ($this->isAjaxRequest()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => 'Não autorizado']);
        } else {
            header('Location: /login.php');
        }
        exit;
    }
    
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
?>