<?php
// =============================================
// FICHIER : controllers/AuthController.php
// RÔLE    : Gère l'inscription, la connexion
//           et la déconnexion des utilisateurs
//
// PRINCIPE MVC : Le contrôleur reçoit la requête
//               HTTP, appelle le modèle User,
//               puis inclut la vue appropriée.
// =============================================

require_once 'models/User.php';

class AuthController {

    private $pdo;
    private $userModel; // Instance du modèle User

    /**
     * Constructeur
     * Reçoit $pdo depuis index.php et crée une instance du modèle
     */
    public function __construct($pdo) {
        $this->pdo       = $pdo;
        $this->userModel = new User($pdo);
    }

    // ==========================================
    // ACTION : login
    // ==========================================

    /**
     * Affiche le formulaire de connexion (GET)
     * Vérifie les identifiants et connecte l'utilisateur (POST)
     *
     * Processus :
     *   1. Si le formulaire est soumis → récupérer email + mot de passe
     *   2. Chercher l'utilisateur en BDD par son email
     *   3. Vérifier le mot de passe avec password_verify()
     *   4. Si OK → créer la session et rediriger
     *   5. Si NON → réafficher le formulaire avec une erreur
     */
    public function login() {
        $error = ''; // Message d'erreur à passer à la vue

        // Vérifier si le formulaire a été soumis (méthode POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Récupérer et nettoyer les données du formulaire
            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validation basique
            if (empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs.';

            } else {
                // Chercher l'utilisateur dans la BDD par son email
                $user = $this->userModel->findByEmail($email);

                // password_verify() compare le mot de passe saisi avec le hash stocké
                if ($user && password_verify($password, $user['mot_de_passe'])) {

                    // ✅ Connexion réussie !
                    // Stocker les infos de l'utilisateur en session
                    $_SESSION['user_id']     = $user['id'];
                    $_SESSION['user_nom']    = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];
                    $_SESSION['user_role']   = $user['role'];

                    // Rediriger vers la page des matches
                    header('Location: index.php?controller=match&action=list');
                    exit;

                } else {
                    // ❌ Email ou mot de passe incorrect
                    $error = 'Email ou mot de passe incorrect.';
                }
            }
        }

        // Afficher la vue de connexion
        // $error est disponible dans la vue grâce à la portée locale
        $pageTitle = 'Connexion — DevMates';
        $pdo = $this->pdo; // Rendre $pdo accessible à header.php
        include 'views/layout/header.php';
        include 'views/auth/login.php';
        include 'views/layout/footer.php';
    }

    // ==========================================
    // ACTION : register
    // ==========================================

    /**
     * Affiche le formulaire d'inscription (GET)
     * Crée un nouvel utilisateur en BDD (POST)
     *
     * Processus :
     *   1. Si formulaire soumis → valider les données
     *   2. Vérifier que l'email n'existe pas déjà
     *   3. Hasher le mot de passe avec password_hash()
     *   4. Créer l'utilisateur en BDD
     *   5. Connecter automatiquement et rediriger
     */
    public function register() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Récupérer et nettoyer les données du formulaire
            $nom         = trim($_POST['nom']         ?? '');
            $prenom      = trim($_POST['prenom']       ?? '');
            $email       = trim($_POST['email']        ?? '');
            $password    = trim($_POST['password']     ?? '');
            $role        = trim($_POST['role']         ?? 'dev');
            $competences = trim($_POST['competences']  ?? '');
            $bio         = trim($_POST['bio']          ?? '');

            // --- Validation des champs ---
            if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
                $error = 'Veuillez remplir tous les champs obligatoires (*).';

            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Vérifier le format de l'email (ex: user@domaine.fr)
                $error = 'L\'adresse email n\'est pas valide.';

            } elseif (strlen($password) < 6) {
                $error = 'Le mot de passe doit contenir au moins 6 caractères.';

            } elseif (!in_array($role, ['dev', 'admin'])) {
                // Vérifier que le rôle est valide (éviter la manipulation de formulaire)
                $error = 'Rôle invalide.';

            } elseif ($this->userModel->emailExists($email)) {
                $error = 'Cette adresse email est déjà utilisée.';

            } else {
                // ✅ Toutes les validations sont OK

                // IMPORTANT : Ne jamais stocker un mot de passe en clair !
                // password_hash() génère un hash sécurisé (bcrypt par défaut)
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Créer l'utilisateur en BDD
                $userId = $this->userModel->create(
                    $nom, $prenom, $email, $hashedPassword, $role, $competences, $bio
                );

                // Connecter automatiquement l'utilisateur après son inscription
                $_SESSION['user_id']     = $userId;
                $_SESSION['user_nom']    = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_role']   = $role;

                // Rediriger vers la page des matches
                header('Location: index.php?controller=match&action=list');
                exit;
            }
        }

        // Afficher le formulaire d'inscription
        $pageTitle = 'Inscription — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/auth/register.php';
        include 'views/layout/footer.php';
    }

    // ==========================================
    // ACTION : logout
    // ==========================================

    /**
     * Déconnecte l'utilisateur
     * Détruit toutes les données de session et redirige vers l'accueil
     */
    public function logout() {
        // Vider le tableau de session
        $_SESSION = [];

        // Supprimer le cookie de session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // Détruire la session côté serveur
        session_destroy();

        // Retourner à la page d'accueil
        header('Location: index.php');
        exit;
    }
}
