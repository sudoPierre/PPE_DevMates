<?php
// =============================================
// FICHIER : index.php
// RÔLE    : Point d'entrée UNIQUE du site
//           (Front Controller)
//           Toutes les pages passent par ici.
//
// FONCTIONNEMENT :
//   L'URL contient deux paramètres GET :
//     - controller : quel contrôleur appeler
//     - action     : quelle méthode exécuter
//
//   Exemples d'URLs :
//     index.php                               → Page d'accueil
//     index.php?controller=auth&action=login  → Page de connexion
//     index.php?controller=match&action=list  → Page des matches
// =============================================

// 1. Démarrer la session PHP (stocke les infos de l'utilisateur connecté)
session_start();

// 2. Charger la connexion à la base de données (crée la variable $pdo)
require_once 'config/db.php';

// 3. Lire les paramètres de l'URL
//    ?? '' signifie : si la variable n'existe pas, utiliser une chaîne vide
$controller = $_GET['controller'] ?? 'home';
$action     = $_GET['action']     ?? 'index';

// 4. Sécuriser les paramètres : n'autoriser que des lettres (a-z, A-Z)
//    Cela empêche d'injecter du code via l'URL
$controller = preg_replace('/[^a-zA-Z]/', '', $controller);
$action     = preg_replace('/[^a-zA-Z]/', '', $action);

// 5. Router vers le bon contrôleur selon le paramètre "controller"
switch ($controller) {

    // ---- Authentification (connexion / inscription / déconnexion) ----
    case 'auth':
        require_once 'controllers/AuthController.php';
        $ctrl = new AuthController($pdo);
        break;

    // ---- Profil utilisateur ----
    case 'profile':
        require_once 'controllers/ProfileController.php';
        $ctrl = new ProfileController($pdo);
        break;

    // ---- Algorithme de matching ----
    case 'match':
        require_once 'controllers/MatchController.php';
        $ctrl = new MatchController($pdo);
        break;

    // ---- Messagerie ----
    case 'message':
        require_once 'controllers/MessageController.php';
        $ctrl = new MessageController($pdo);
        break;

    // ---- Pages statiques : FAQ / Mentions légales ----
    case 'legal':
        $action = 'legal';
        require_once 'controllers/StaticController.php';
        $ctrl = new StaticController($pdo);
        break;
    case 'faq':
        $action = 'faq';
        require_once 'controllers/StaticController.php';
        $ctrl = new StaticController($pdo);
        break;

    // ---- Page d'accueil (cas par défaut) ----
    default:
        // Pas de contrôleur → afficher directement la landing page
        $pageTitle = 'DevMates Trouve ton développeur idéal';
        include 'views/layout/header.php';
        include 'views/home.php';
        include 'views/layout/footer.php';
        exit; // Stopper le script ici pour ne pas exécuter la suite
}

// 6. Vérifier que la méthode demandée (action) existe dans le contrôleur
//    method_exists() retourne true si la méthode publique existe
if (method_exists($ctrl, $action)) {
    // Appeler dynamiquement la méthode du contrôleur
    // Exemple : $action = "login" → $ctrl->login()
    $ctrl->$action();

} else {
    // La page demandée n'existe pas → erreur 404
    http_response_code(404);
    $pageTitle = '404 — Page introuvable';
    include 'views/layout/header.php';
    echo '
    <div class="container text-center py-5">
        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3 d-block"></i>
        <h1 class="fw-bold">404 — Page introuvable</h1>
        <p class="text-muted">La page que tu cherches n\'existe pas.</p>
        <a href="index.php" class="btn btn-primary mt-2">
            <i class="fas fa-home me-2"></i>Retour à l\'accueil
        </a>
    </div>';
    include 'views/layout/footer.php';
}
