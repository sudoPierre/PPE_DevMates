<?php
// =============================================
// FICHIER : models/User.php
// RÔLE    : Gère TOUTES les opérations SQL
//           sur la table "users"
//           (CRUD = Create / Read / Update / Delete)
//
// PRINCIPE MVC : Le modèle ne fait QUE des
//               requêtes SQL. Il ne sait pas
//               d'où viennent les données ni
//               comment elles seront affichées.
// =============================================

class User {

    // Connexion PDO reçue depuis le contrôleur
    private $pdo;

    /**
     * Constructeur
     * Reçoit la connexion PDO créée dans config/db.php
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ==========================================
    // READ — Lire des données
    // ==========================================

    /**
     * Cherche un utilisateur par son adresse email
     * Utilisé lors de la connexion pour vérifier les identifiants
     *
     * @param  string $email  L'email saisi dans le formulaire
     * @return array|false    Les données de l'utilisateur ou false si introuvable
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );
        // :email est un marqueur nommé → empêche les injections SQL
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Cherche un utilisateur par son ID
     * Utilisé pour afficher ou modifier un profil
     *
     * @param  int   $id  L'identifiant de l'utilisateur
     * @return array|false
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Récupère tous les développeurs SAUF l'utilisateur connecté
     * Utilisé par l'algorithme de matching pour trouver des profils
     *
     * @param  int   $userId  L'ID de l'utilisateur connecté (à exclure)
     * @return array          Tableau de tous les autres développeurs
     */
    public function getAllExcept($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT id, nom, prenom, email, role, competences, bio, date_inscription
             FROM users
             WHERE id != :id AND role = 'dev'
             ORDER BY nom ASC"
        );
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchAll(); // fetchAll() retourne un tableau de tous les résultats
    }

    /**
     * Vérifie si un email est déjà utilisé dans la base
     * Empêche deux comptes d'avoir le même email
     *
     * @param  string $email
     * @return bool   true si l'email existe déjà
     */
    public function emailExists($email) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM users WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        // fetchColumn() retourne la valeur de la première colonne du premier résultat
        return $stmt->fetchColumn() > 0;
    }

    // ==========================================
    // CREATE — Créer un utilisateur
    // ==========================================

    /**
     * Insère un nouvel utilisateur en base de données
     * Appelé lors de l'inscription
     *
     * @param  string $nom
     * @param  string $prenom
     * @param  string $email
     * @param  string $hashedPassword  Mot de passe déjà hashé avec password_hash()
     * @param  string $role            'dev' ou 'admin'
     * @param  string $competences     Ex : "PHP,Python,MySQL"
     * @param  string $bio
     * @return int                     L'ID du nouvel utilisateur créé
     */
    public function create($nom, $prenom, $email, $hashedPassword, $role, $competences, $bio) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (nom, prenom, email, mot_de_passe, role, competences, bio)
             VALUES (:nom, :prenom, :email, :mdp, :role, :competences, :bio)"
        );
        $stmt->execute([
            ':nom'         => $nom,
            ':prenom'      => $prenom,
            ':email'       => $email,
            ':mdp'         => $hashedPassword,
            ':role'        => $role,
            ':competences' => $competences,
            ':bio'         => $bio,
        ]);
        // Retourner l'ID généré automatiquement par MySQL (AUTO_INCREMENT)
        return $this->pdo->lastInsertId();
    }

    // ==========================================
    // UPDATE — Modifier un utilisateur
    // ==========================================

    /**
     * Met à jour le profil d'un utilisateur
     * L'utilisateur peut modifier son email, ses compétences et sa bio
     *
     * @param int    $id
     * @param string $email
     * @param string $competences
     * @param string $bio
     */
    public function update($id, $email, $competences, $bio) {
        $stmt = $this->pdo->prepare(
            "UPDATE users
             SET email = :email, competences = :competences, bio = :bio
             WHERE id = :id"
        );
        $stmt->execute([
            ':email'       => $email,
            ':competences' => $competences,
            ':bio'         => $bio,
            ':id'          => $id,
        ]);
    }
}
