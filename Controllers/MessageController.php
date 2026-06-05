<?php
// =============================================
// FICHIER : controllers/MessageController.php
// RÔLE    : Gère l'envoi et la réception
//           de messages entre utilisateurs
// =============================================

require_once 'models/Message.php';
require_once 'models/User.php';

class MessageController {

    private $pdo;
    private $messageModel;
    private $userModel;

    /**
     * Constructeur : initialise les deux modèles
     * (Message pour les requêtes sur les messages,
     *  User pour vérifier que le destinataire existe)
     */
    public function __construct($pdo) {
        $this->pdo          = $pdo;
        $this->messageModel = new Message($pdo);
        $this->userModel    = new User($pdo);
    }

    /**
     * Vérifie la connexion
     */
    private function requireLogin() {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // ==========================================
    // ACTION : inbox
    // ==========================================

    /**
     * Affiche la boîte de réception de l'utilisateur connecté
     *
     * Processus :
     *   1. Marquer tous les messages reçus comme lus
     *   2. Récupérer les messages reçus (avec nom de l'expéditeur)
     *   3. Récupérer les messages envoyés (avec nom du destinataire)
     *   4. Passer tout ça à la vue
     */
    public function inbox() {
        $this->requireLogin();

        // Marquer tous les messages non lus comme lus maintenant qu'on ouvre la boîte
        $this->messageModel->markAllAsRead($_SESSION['user_id']);

        // Récupérer les messages reçus
        $received = $this->messageModel->getInbox($_SESSION['user_id']);

        // Récupérer les messages envoyés
        $sent = $this->messageModel->getSent($_SESSION['user_id']);

        // Afficher la vue messagerie
        $pageTitle = 'Messagerie — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/messages/inbox.php';
        include 'views/layout/footer.php';
    }

    // ==========================================
    // ACTION : send
    // ==========================================

    /**
     * Traite l'envoi d'un message (POST uniquement)
     * Reçoit les données depuis le formulaire de la page de matching
     *
     * Processus :
     *   1. Vérifier que les données sont valides (destinataire + contenu)
     *   2. Vérifier que le destinataire existe réellement en BDD
     *   3. Insérer le message en BDD
     *   4. Rediriger vers la boîte de réception
     */
    public function send() {
        $this->requireLogin();

        // Cette action ne traite que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // intval() convertit en entier (sécurité contre les valeurs non numériques)
            $idDestinataire = intval($_POST['id_destinataire'] ?? 0);
            $contenu        = trim($_POST['contenu'] ?? '');

            // Vérifications basiques avant d'envoyer
            if ($idDestinataire > 0 && !empty($contenu) && $idDestinataire !== (int)$_SESSION['user_id']) {

                // Vérifier que le destinataire existe vraiment en BDD
                $destinataire = $this->userModel->findById($idDestinataire);

                if ($destinataire) {
                    // ✅ Tout est OK → envoyer le message
                    $this->messageModel->send(
                        $_SESSION['user_id'],
                        $idDestinataire,
                        $contenu
                    );
                }
            }
        }

        // Toujours rediriger vers la boîte de réception après l'envoi
        // (Pattern PRG : Post/Redirect/Get — évite la resoumission du formulaire)
        header('Location: index.php?controller=message&action=inbox');
        exit;
    }
}
