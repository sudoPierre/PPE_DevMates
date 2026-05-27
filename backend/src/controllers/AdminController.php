<?php

declare(strict_types=1);

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use PDO;

/**
 * Contrôleur d'administration
 * Toutes les routes de ce contrôleur sont réservées au rôle admin
 */
class AdminController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/admin/users
     * Liste tous les utilisateurs avec leur profil associé
     */
    public function getUsers(): void
    {
        AuthMiddleware::verifyAdmin();

        $stmt = $this->db->query(
            'SELECT u.id, u.email, u.role, u.is_active, u.is_banned, u.created_at,
                    p.display_name, p.avatar_url
             FROM Users u
             LEFT JOIN Profiles p ON p.user_id = u.id
             ORDER BY u.created_at DESC'
        );

        echo json_encode($stmt->fetchAll());
    }

    /**
     * PUT /api/admin/users/:id
     * Modifie le rôle, le statut actif ou le statut banni d'un utilisateur
     */
    public function updateUser(string $id): void
    {
        AuthMiddleware::verifyAdmin();
        $data    = (array) (json_decode(file_get_contents('php://input') ?: '{}', true) ?? []);

        $allowed  = ['is_active', 'is_banned', 'role'];
        $updates  = [];
        $values   = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "{$field} = ?";
                $values[]  = $data[$field];
            }
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Aucune donnée valide fournie pour la mise à jour.']);
            return;
        }

        $values[] = $id;
        $this->db->prepare(
            'UPDATE Users SET ' . implode(', ', $updates) . ' WHERE id = ?'
        )->execute($values);

        echo json_encode(['message' => 'Utilisateur mis à jour avec succès.']);
    }

    /**
     * DELETE /api/admin/users/:id
     * Supprime définitivement un utilisateur (cascade sur profil, matchs, messages)
     */
    public function deleteUser(string $id): void
    {
        AuthMiddleware::verifyAdmin();

        $stmt = $this->db->prepare('SELECT id FROM Users WHERE id = ?');
        $stmt->execute([$id]);

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Utilisateur introuvable.']);
            return;
        }

        $this->db->prepare('DELETE FROM Users WHERE id = ?')->execute([$id]);

        echo json_encode(['message' => 'Utilisateur supprimé définitivement.']);
    }

    /**
     * POST /api/admin/users/:id/ban
     * Bannit un utilisateur (raccourci sans body requis)
     */
    public function banUser(string $id): void
    {
        AuthMiddleware::verifyAdmin();

        $stmt = $this->db->prepare('SELECT id FROM Users WHERE id = ?');
        $stmt->execute([$id]);

        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Utilisateur introuvable.']);
            return;
        }

        $this->db->prepare(
            'UPDATE Users SET is_banned = 1 WHERE id = ?'
        )->execute([$id]);

        echo json_encode(['message' => 'Utilisateur banni avec succès.']);
    }
}
