<?php
// =============================================
// FICHIER : controllers/MatchController.php
// RÔLE    : Calcule et affiche les scores de
//           compatibilité entre développeurs
//
// ALGORITHME DE MATCHING :
//   Pour chaque autre développeur on calcule :
//   score = (compétences communes / total unique) × 100
//   Ensuite on trie du plus compatible au moins.
// =============================================

require_once 'models/User.php';

class MatchController {

    private $pdo;
    private $userModel;

    /**
     * Constructeur
     */
    public function __construct($pdo) {
        $this->pdo       = $pdo;
        $this->userModel = new User($pdo);
    }

    /**
     * Vérifie la connexion avant d'accéder à la page
     */
    private function requireLogin() {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }
    }

    // ==========================================
    // ACTION : list
    // ==========================================

    /**
     * Récupère tous les développeurs, calcule leur score
     * de compatibilité avec l'utilisateur connecté,
     * trie par score décroissant et affiche la liste.
     */
    public function list() {
        $this->requireLogin();

        // 1. Récupérer le profil complet de l'utilisateur connecté
        $currentUser = $this->userModel->findById($_SESSION['user_id']);

        // 2. Récupérer tous les autres développeurs (sauf soi-même)
        $otherUsers = $this->userModel->getAllExcept($_SESSION['user_id']);

        // 3. Calculer le score de compatibilité pour chaque développeur
        $matches = $this->calculateScores($currentUser, $otherUsers);

        // 4. Trier par score décroissant (le plus compatible en premier)
        usort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        // 5. Afficher la vue
        $pageTitle = 'Mes Matches — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/match/list.php';
        include 'views/layout/footer.php';
    }

    // ==========================================
    // MÉTHODES PRIVÉES (logique métier)
    // ==========================================

    /**
     * Calcule le score de compatibilité entre l'utilisateur courant
     * et chacun des autres développeurs.
     *
     * Formule : (nb compétences communes / nb total compétences uniques) × 100
     *
     * Exemple :
     *   Moi   : PHP, Python, MySQL          → 3 compétences
     *   Autre : PHP, JavaScript, MySQL      → 3 compétences
     *   Communes        : PHP, MySQL        → 2
     *   Total unique    : PHP, Python, MySQL, JavaScript → 4
     *   Score = 2/4 × 100 = 50%
     *
     * @param  array $currentUser  Données de l'utilisateur connecté
     * @param  array $otherUsers   Tableau des autres développeurs
     * @return array               Tableau $otherUsers enrichi du score
     */
    private function calculateScores($currentUser, $otherUsers) {
        $matches = [];

        // Convertir les compétences de l'utilisateur connecté en tableau
        $mySkills = $this->parseSkills($currentUser['competences']);

        foreach ($otherUsers as $user) {
            // Convertir les compétences de l'autre développeur en tableau
            $theirSkills = $this->parseSkills($user['competences']);

            // Compétences EN COMMUN (intersection des deux tableaux)
            $common = array_intersect($mySkills, $theirSkills);

            // Compétences TOTALES uniques (union des deux tableaux)
            $total = array_unique(array_merge($mySkills, $theirSkills));

            // Calcul du score (éviter la division par zéro si les deux n'ont aucune compétence)
            $score = count($total) > 0
                ? (int) round((count($common) / count($total)) * 100)
                : 0;

            // Enrichir le tableau de l'utilisateur avec les données de matching
            $user['score']        = $score;
            $user['commonSkills'] = array_values($common);  // Compétences communes
            $user['allSkills']    = $theirSkills;            // Toutes ses compétences

            $matches[] = $user;
        }

        return $matches;
    }

    /**
     * Convertit une chaîne de compétences "PHP,Python,JS"
     * en tableau PHP nettoyé et en minuscules pour la comparaison
     *
     * @param  string $competencesString  Ex : "PHP, Python, MySQL"
     * @return array                      Ex : ['php', 'python', 'mysql']
     */
    private function parseSkills($competencesString) {
        if (empty(trim($competencesString))) {
            return [];
        }
        // Séparer par virgule, supprimer les espaces, mettre en minuscules
        return array_filter(
            array_map('trim', explode(',', strtolower($competencesString)))
        );
    }
}
