<?php
// config/jwt.php
class JWTHandler {
    private $secret_key = "sua_chave_secreta_aqui";
    private $algorithm = 'HS256';

    public function generateToken(array $user_data): string {
        $header = json_encode(['typ' => 'JWT', 'alg' => $this->algorithm]);
        $payload = json_encode([
            'user_id' => $user_data['id'],
            'email'   => $user_data['email'],
            'nome'    => $user_data['nome'],
            'tipo'    => $user_data['tipo'],
            'iat'     => time(),
            'exp'     => time() + 86400 // 24h
        ]);

        $base64Header  = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64Payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret_key, true);
        $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64Header.$base64Payload.$base64Signature";
    }

    public function validateToken(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        [$header64, $payload64, $signature] = $parts;

        $expected = rtrim(strtr(base64_encode(
            hash_hmac('sha256', "$header64.$payload64", $this->secret_key, true)
        ), '+/', '-_'), '=');

        if (!hash_equals($expected, $signature)) return false;

        $payload = json_decode(base64_decode(strtr($payload64, '-_', '+/')), true);

        return ($payload && $payload['exp'] > time()) ? $payload : false;
    }
}
