<?php
// =============================================
// FICHIER : models/Message.php
// RÔLE    : Gère TOUTES les opérations SQL
//           sur la table "messages"
//           (envoi, réception, marquage lu)
// =============================================

class Message {

    private $pdo;

    /**
     * Constructeur : reçoit la connexion PDO
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ==========================================
    // CREATE — Envoyer un message
    // ==========================================

    /**
     * Insère un message en base de données
     * Le champ "lu" est à 0 par défaut (non lu)
     *
     * @param int    $idExpediteur    ID de celui qui envoie
     * @param int    $idDestinataire  ID de celui qui reçoit
     * @param string $contenu         Texte du message
     */
    public function send($idExpediteur, $idDestinataire, $contenu) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO messages (id_expediteur, id_destinataire, contenu)
             VALUES (:exp, :dest, :contenu)"
        );
        $stmt->execute([
            ':exp'     => $idExpediteur,
            ':dest'    => $idDestinataire,
            ':contenu' => $contenu,
        ]);
    }

    // ==========================================
    // READ — Lire des messages
    // ==========================================

    /**
     * Récupère tous les messages REÇUS par un utilisateur
     * Fait une jointure avec "users" pour avoir le nom de l'expéditeur
     *
     * @param  int   $userId  ID du destinataire
     * @return array          Tableau de messages avec nom/prénom de l'expéditeur
     */
    public function getInbox($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT m.id, m.contenu, m.date_envoi, m.lu,
                    u.nom, u.prenom, u.id AS id_expediteur
             FROM messages m
             -- JOIN pour récupérer le nom de l'expéditeur
             JOIN users u ON u.id = m.id_expediteur
             WHERE m.id_destinataire = :userId
             ORDER BY m.date_envoi DESC"
        );
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Récupère tous les messages ENVOYÉS par un utilisateur
     * Fait une jointure pour avoir le nom du destinataire
     *
     * @param  int   $userId  ID de l'expéditeur
     * @return array
     */
    public function getSent($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT m.id, m.contenu, m.date_envoi,
                    u.nom, u.prenom, u.id AS id_destinataire
             FROM messages m
             JOIN users u ON u.id = m.id_destinataire
             WHERE m.id_expediteur = :userId
             ORDER BY m.date_envoi DESC"
        );
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll();
    }

    // ==========================================
    // UPDATE — Marquer comme lu
    // ==========================================

    /**
     * Marque TOUS les messages non lus d'un utilisateur comme lus
     * Appelé automatiquement à l'ouverture de la boîte de réception
     *
     * @param int $userId  ID du destinataire
     */
    public function markAllAsRead($userId) {
        $stmt = $this->pdo->prepare(
            "UPDATE messages SET lu = 1
             WHERE id_destinataire = :userId AND lu = 0"
        );
        $stmt->execute([':userId' => $userId]);
    }

    /**
     * Compte les messages non lus d'un utilisateur
     * Utilisé pour afficher le badge de notification dans la navbar
     *
     * @param  int $userId
     * @return int          Nombre de messages non lus
     */
    public function countUnread($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM messages
             WHERE id_destinataire = :userId AND lu = 0"
        );
        $stmt->execute([':userId' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
