<?php

declare(strict_types=1);

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use PDO;

/**
 * Contrôleur d'authentification
 * Gère l'inscription et la connexion des utilisateurs
 */
class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * POST /api/auth/register
     * Crée un nouveau compte utilisateur et retourne un token JWT
     */
    public function register(): void
    {
        $data = $this->getJsonBody();

        if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Les champs email, password et role sont obligatoires.']);
            return;
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['erreur' => "Format d'adresse email invalide."]);
            return;
        }

        if (!in_array($data['role'], ['developpeur', 'porteur_projet'], true)) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Rôle invalide. Valeurs acceptées : developpeur, porteur_projet.']);
            return;
        }

        if (strlen($data['password']) < 8) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Le mot de passe doit contenir au moins 8 caractères.']);
            return;
        }

        $stmt = $this->db->prepare('SELECT id FROM Users WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['erreur' => 'Cette adresse email est déjà utilisée.']);
            return;
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare(
            'INSERT INTO Users (email, password_hash, role) VALUES (?, ?, ?)'
        );
        $stmt->execute([$data['email'], $hash, $data['role']]);
        $userId = (int) $this->db->lastInsertId();

        $token = AuthMiddleware::generateJWT([
            'sub'   => $userId,
            'email' => $data['email'],
            'role'  => $data['role'],
        ]);

        http_response_code(201);
        echo json_encode([
            'message' => 'Compte créé avec succès.',
            'token'   => $token,
            'user'    => ['id' => $userId, 'email' => $data['email'], 'role' => $data['role']],
        ]);
    }

    /**
     * POST /api/auth/login
     * Authentifie un utilisateur et retourne un token JWT
     */
    public function login(): void
    {
        $data = $this->getJsonBody();

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['erreur' => "L'email et le mot de passe sont requis."]);
            return;
        }

        $stmt = $this->db->prepare(
            'SELECT id, email, password_hash, role, is_banned
             FROM Users WHERE email = ? AND is_active = 1'
        );
        $stmt->execute([$data['email']]);
        $user = $stmt->fetch();

        // Message générique pour ne pas révéler si l'email existe
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['erreur' => 'Identifiants incorrects.']);
            return;
        }

        if ($user['is_banned']) {
            http_response_code(403);
            echo json_encode(['erreur' => "Votre compte a été banni. Contactez l'administrateur."]);
            return;
        }

        $token = AuthMiddleware::generateJWT([
            'sub'   => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        echo json_encode([
            'message' => 'Connexion réussie.',
            'token'   => $token,
            'user'    => ['id' => $user['id'], 'email' => $user['email'], 'role' => $user['role']],
        ]);
    }

    /** @return array<string, mixed> */
    private function getJsonBody(): array
    {
        return (array) (json_decode(file_get_contents('php://input') ?: '{}', true) ?? []);
    }
}
