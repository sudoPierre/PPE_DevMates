<?php

declare(strict_types=1);

namespace Controllers;

use Config\Database;
use Middleware\AuthMiddleware;
use PDO;

/**
 * Contrôleur de messagerie
 * Support du Short Polling via le paramètre ?since= (timestamp ISO)
 */
class MessageController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/messages/:matchId
     * Récupère les messages d'une conversation
     * Avec ?since=2024-01-01T12:00:00 → retourne uniquement les nouveaux messages (Short Polling)
     */
    public function getMessages(string $matchId): void
    {
        $payload = AuthMiddleware::verify();
        $userId  = (int) $payload['sub'];

        $this->assertMatchAccess((int) $matchId, $userId);

        $since = $_GET['since'] ?? null;

        if ($since !== null) {
            $stmt = $this->db->prepare(
                'SELECT m.id, m.sender_id, m.content, m.sent_at, p.display_name AS sender_name
                 FROM Messages m
                 JOIN Profiles p ON p.user_id = m.sender_id
                 WHERE m.match_id = ? AND m.sent_at > ?
                 ORDER BY m.sent_at ASC'
            );
            $stmt->execute([$matchId, $since]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT m.id, m.sender_id, m.content, m.sent_at, p.display_name AS sender_name
                 FROM Messages m
                 JOIN Profiles p ON p.user_id = m.sender_id
                 WHERE m.match_id = ?
                 ORDER BY m.sent_at ASC
                 LIMIT 100'
            );
            $stmt->execute([$matchId]);
        }

        // Marquer comme lus les messages reçus par l'utilisateur courant
        $this->db->prepare(
            'UPDATE Messages SET is_read = 1 WHERE match_id = ? AND sender_id != ? AND is_read = 0'
        )->execute([$matchId, $userId]);

        echo json_encode($stmt->fetchAll());
    }

    /**
     * POST /api/messages/:matchId
     * Envoie un nouveau message dans une conversation (match validé requis)
     */
    public function sendMessage(string $matchId): void
    {
        $payload = AuthMiddleware::verify();
        $userId  = (int) $payload['sub'];
        $data    = (array) (json_decode(file_get_contents('php://input') ?: '{}', true) ?? []);

        if (empty($data['content']) || trim($data['content']) === '') {
            http_response_code(400);
            echo json_encode(['erreur' => 'Le contenu du message ne peut pas être vide.']);
            return;
        }

        $this->assertMatchAccess((int) $matchId, $userId);

        $stmt = $this->db->prepare(
            'INSERT INTO Messages (match_id, sender_id, content) VALUES (?, ?, ?)'
        );
        $stmt->execute([$matchId, $userId, trim($data['content'])]);

        http_response_code(201);
        echo json_encode([
            'message' => 'Message envoyé.',
            'id'      => (int) $this->db->lastInsertId(),
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Vérifie que l'utilisateur est bien participant au match validé
     * Termine avec 403 si ce n'est pas le cas
     */
    private function assertMatchAccess(int $matchId, int $userId): void
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM Matches
             WHERE id = ? AND is_matched = 1 AND (user_id_1 = ? OR user_id_2 = ?)'
        );
        $stmt->execute([$matchId, $userId, $userId]);

        if (!$stmt->fetch()) {
            http_response_code(403);
            echo json_encode(['erreur' => "Accès refusé à cette conversation."]);
            exit();
        }
    }
}
