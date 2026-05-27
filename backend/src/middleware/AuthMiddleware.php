<?php

declare(strict_types=1);

namespace Middleware;

/**
 * Middleware d'authentification JWT (HS256) sans dépendance externe
 * Vérifie la signature et l'expiration du token dans chaque requête protégée
 */
class AuthMiddleware
{
    /**
     * Vérifie le token JWT et retourne le payload — termine avec 401 si invalide
     *
     * @return array<string, mixed>
     */
    public static function verify(): array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            http_response_code(401);
            echo json_encode(['erreur' => "Token d'authentification manquant ou malformé."]);
            exit();
        }

        $payload = self::decodeAndVerify($matches[1]);

        if ($payload === null) {
            http_response_code(401);
            echo json_encode(['erreur' => 'Token invalide ou expiré. Veuillez vous reconnecter.']);
            exit();
        }

        return $payload;
    }

    /**
     * Vérifie que l'utilisateur connecté est administrateur
     *
     * @return array<string, mixed>
     */
    public static function verifyAdmin(): array
    {
        $payload = self::verify();

        if (($payload['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['erreur' => 'Accès refusé. Cette ressource est réservée aux administrateurs.']);
            exit();
        }

        return $payload;
    }

    /**
     * Génère un token JWT signé valable 24 heures
     *
     * @param array<string, mixed> $payload
     */
    public static function generateJWT(array $payload): string
    {
        $secret  = self::getSecret();
        $header  = self::b64Encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['iat'] = time();
        $payload['exp'] = time() + 86400; // 24 heures
        $claims  = self::b64Encode(json_encode($payload));
        $sig     = self::b64Encode(hash_hmac('sha256', "{$header}.{$claims}", $secret, true));

        return "{$header}.{$claims}.{$sig}";
    }

    /** @return array<string, mixed>|null */
    private static function decodeAndVerify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $claims, $sig] = $parts;
        $secret      = self::getSecret();
        $expectedSig = self::b64Encode(hash_hmac('sha256', "{$header}.{$claims}", $secret, true));

        // Comparaison en temps constant pour prévenir les attaques temporelles
        if (!hash_equals($expectedSig, $sig)) {
            return null;
        }

        $payload = json_decode(self::b64Decode($claims), true);

        if (!is_array($payload) || !isset($payload['exp']) || $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    private static function getSecret(): string
    {
        return $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?: 'changez_ce_secret_en_production';
    }

    private static function b64Encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64Decode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
