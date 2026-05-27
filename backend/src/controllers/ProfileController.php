<?php

declare(strict_types=1);

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use PDO;

/**
 * Contrôleur de gestion des profils utilisateurs
 */
class ProfileController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/profile
     * Retourne le profil complet de l'utilisateur connecté
     */
    public function getMyProfile(): void
    {
        $payload = AuthMiddleware::verify();

        $stmt = $this->db->prepare(
            'SELECT p.*, u.email, u.role
             FROM Profiles p
             JOIN Users u ON u.id = p.user_id
             WHERE p.user_id = ?'
        );
        $stmt->execute([$payload['sub']]);
        $profile = $stmt->fetch();

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Profil introuvable.']);
            return;
        }

        $profile['skills']        = json_decode($profile['skills'] ?? '[]');
        $profile['project_ideas'] = json_decode($profile['project_ideas'] ?? '[]');

        echo json_encode($profile);
    }

    /**
     * PUT /api/profile
     * Met à jour le profil de l'utilisateur connecté
     */
    public function updateMyProfile(): void
    {
        $payload = AuthMiddleware::verify();
        $data    = (array) (json_decode(file_get_contents('php://input') ?: '{}', true) ?? []);

        $allowed = ['display_name', 'bio', 'skills', 'project_ideas', 'github_url', 'linkedin_url', 'avatar_url'];
        $updates = [];
        $values  = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "{$field} = ?";
                $values[]  = is_array($data[$field]) ? json_encode($data[$field]) : $data[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Aucune donnée valide fournie pour la mise à jour.']);
            return;
        }

        $values[] = $payload['sub'];
        $this->db->prepare(
            'UPDATE Profiles SET ' . implode(', ', $updates) . ' WHERE user_id = ?'
        )->execute($values);

        echo json_encode(['message' => 'Profil mis à jour avec succès.']);
    }

    /**
     * GET /api/profile/:id
     * Retourne le profil public d'un autre utilisateur (non banni, actif)
     */
    public function getProfile(string $id): void
    {
        AuthMiddleware::verify();

        $stmt = $this->db->prepare(
            'SELECT p.display_name, p.bio, p.skills, p.project_ideas,
                    p.github_url, p.linkedin_url, p.avatar_url, u.role
             FROM Profiles p
             JOIN Users u ON u.id = p.user_id
             WHERE p.user_id = ? AND u.is_active = 1 AND u.is_banned = 0'
        );
        $stmt->execute([$id]);
        $profile = $stmt->fetch();

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Profil introuvable.']);
            return;
        }

        $profile['skills']        = json_decode($profile['skills'] ?? '[]');
        $profile['project_ideas'] = json_decode($profile['project_ideas'] ?? '[]');

        echo json_encode($profile);
    }
}
