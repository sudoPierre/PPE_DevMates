<?php

declare(strict_types=1);

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use PDO;

/**
 * Contrôleur de gestion des matchs
 * Gère les swipes (like/dislike) et la récupération des matchs validés
 */
class MatchController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/match/candidates
     * Retourne jusqu'à 10 profils que l'utilisateur n'a pas encore vus
     */
    public function getCandidates(): void
    {
        $payload = AuthMiddleware::verify();
        $userId  = (int) $payload['sub'];

        $stmt = $this->db->prepare(
            'SELECT p.user_id, p.display_name, p.bio, p.skills, p.project_ideas, p.avatar_url, u.role
             FROM Profiles p
             JOIN Users u ON u.id = p.user_id
             WHERE u.id != :uid
               AND u.is_active = 1
               AND u.is_banned = 0
               AND u.id NOT IN (
                   SELECT user_id_2 FROM Matches WHERE user_id_1 = :uid2
                   UNION
                   SELECT user_id_1 FROM Matches WHERE user_id_2 = :uid3
               )
             ORDER BY RAND()
             LIMIT 10'
        );
        $stmt->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
        $candidates = $stmt->fetchAll();

        foreach ($candidates as &$c) {
            $c['skills']        = json_decode($c['skills'] ?? '[]');
            $c['project_ideas'] = json_decode($c['project_ideas'] ?? '[]');
        }

        echo json_encode($candidates);
    }

    /**
     * POST /api/match/action
     * Enregistre un like ou un dislike et détecte un éventuel match mutuel
     */
    public function recordAction(): void
    {
        $payload  = AuthMiddleware::verify();
        $userId   = (int) $payload['sub'];
        $data     = (array) (json_decode(file_get_contents('php://input') ?: '{}', true) ?? []);

        if (empty($data['target_id']) || empty($data['action'])) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Les champs target_id et action sont requis.']);
            return;
        }

        if (!in_array($data['action'], ['like', 'dislike'], true)) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Action invalide. Valeurs acceptées : like, dislike.']);
            return;
        }

        $targetId = (int) $data['target_id'];

        // Vérifie si la cible a déjà agi en premier (entrée avec user_id_1 = target)
        $stmt = $this->db->prepare(
            'SELECT id, action_user_1 FROM Matches WHERE user_id_1 = ? AND user_id_2 = ?'
        );
        $stmt->execute([$targetId, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // L'autre a déjà swipé — on complète la paire et calcule le match
            $isMatched  = ($existing['action_user_1'] === 'like' && $data['action'] === 'like');
            $matchedAt  = $isMatched ? date('Y-m-d H:i:s') : null;

            $this->db->prepare(
                'UPDATE Matches SET action_user_2 = ?, is_matched = ?, matched_at = ? WHERE id = ?'
            )->execute([$data['action'], $isMatched ? 1 : 0, $matchedAt, $existing['id']]);

            echo json_encode([
                'message'    => 'Action enregistrée.',
                'is_matched' => $isMatched,
                'match_id'   => $isMatched ? $existing['id'] : null,
            ]);
        } else {
            // Premier à swiper — création de l'entrée
            $this->db->prepare(
                'INSERT INTO Matches (user_id_1, user_id_2, action_user_1) VALUES (?, ?, ?)'
            )->execute([$userId, $targetId, $data['action']]);

            echo json_encode(['message' => 'Action enregistrée.', 'is_matched' => false]);
        }
    }

    /**
     * GET /api/match
     * Retourne tous les matchs validés (is_matched = 1) de l'utilisateur connecté
     */
    public function getMatches(): void
    {
        $payload = AuthMiddleware::verify();
        $userId  = (int) $payload['sub'];

        $stmt = $this->db->prepare(
            'SELECT m.id AS match_id,
                    m.matched_at,
                    p.user_id,
                    p.display_name,
                    p.avatar_url,
                    u.role
             FROM Matches m
             JOIN Users u ON u.id = IF(m.user_id_1 = :uid, m.user_id_2, m.user_id_1)
             JOIN Profiles p ON p.user_id = u.id
             WHERE m.is_matched = 1
               AND (m.user_id_1 = :uid2 OR m.user_id_2 = :uid3)
             ORDER BY m.matched_at DESC'
        );
        $stmt->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);

        echo json_encode($stmt->fetchAll());
    }
}
