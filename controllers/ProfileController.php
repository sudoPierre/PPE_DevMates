<?php
// =============================================
// FICHIER : controllers/ProfileController.php
// RÔLE    : Gère l'affichage et la modification
//           du profil de l'utilisateur connecté
// =============================================

require_once 'models/User.php';

class ProfileController {

    private $pdo;
    private $userModel;

    /**
     * Constructeur : initialise le modèle User
     */
    public function __construct($pdo) {
        $this->pdo       = $pdo;
        $this->userModel = new User($pdo);
    }

    // ==========================================
    // MÉTHODE PRIVÉE : requireLogin
    // ==========================================

    /**
     * Vérifie que l'utilisateur est bien connecté
     * Si ce n'est pas le cas → redirige vers la page de connexion
     * Cette méthode est appelée au début de chaque action protégée
     */
    private function requireLogin() {
        if (empty($_SESSION['user_id'])) {
            // L'utilisateur n'est pas connecté → on le renvoie au login
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // ==========================================
    // ACTION : edit
    // ==========================================

    /**
     * Affiche le formulaire d'édition du profil (GET)
     * Enregistre les modifications en BDD (POST)
     *
     * L'utilisateur peut modifier :
     *   - Son adresse email
     *   - Ses compétences (liste séparée par des virgules)
     *   - Sa bio (présentation libre)
     */
    public function edit() {
        // Vérifier la connexion avant tout
        $this->requireLogin();

        $error   = '';
        $success = '';

        // Récupérer les données actuelles de l'utilisateur connecté
        $user = $this->userModel->findById($_SESSION['user_id']);

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email       = trim($_POST['email']       ?? '');
            $competences = trim($_POST['competences'] ?? '');
            $bio         = trim($_POST['bio']         ?? '');

            // Validation de l'email
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Veuillez entrer une adresse email valide.';

            } else {
                // Mettre à jour le profil en BDD
                $this->userModel->update($_SESSION['user_id'], $email, $competences, $bio);

                $success = '✅ Profil mis à jour avec succès !';

                // Recharger les données depuis la BDD pour afficher les nouvelles valeurs
                $user = $this->userModel->findById($_SESSION['user_id']);
            }
        }

        // Afficher la vue d'édition du profil
        $pageTitle = 'Mon profil — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/profile/edit.php';
        include 'views/layout/footer.php';
    }
}
