<?php
// =============================================
// FICHIER : config/db.php
// RÔLE    : Connexion à la base de données MySQL
//           via PDO (PHP Data Objects)
//           PDO est plus sécurisé que l'ancienne
//           extension mysqli
// =============================================

// --- Paramètres de connexion ---
// Modifie ces valeurs selon ton environnement XAMPP/WAMP
define('DB_HOST',    'localhost'); // Serveur MySQL
define('DB_NAME',    'devmates');  // Nom de la base de données
define('DB_USER',    'root');      // Utilisateur MySQL (root par défaut sur XAMPP)
define('DB_PASS',    '');          // Mot de passe (vide par défaut sur XAMPP)
define('DB_CHARSET', 'utf8mb4');  // Encodage (supporte les accents et emojis)

try {
    // Créer la connexion PDO
    // Le DSN (Data Source Name) indique : driver, hôte, bdd, encodage
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            // Lancer une exception PHP si une requête SQL échoue
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Les résultats sont retournés sous forme de tableaux associatifs
            // Exemple : $user['email'] au lieu de $user[2]
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Désactiver l'émulation des requêtes préparées (plus sécurisé)
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

} catch (PDOException $e) {
    // Afficher un message d'erreur clair si la connexion échoue
    // (en production, il faudrait logger l'erreur et afficher un message générique)
    die('
    <div style="font-family:Arial;max-width:600px;margin:50px auto;padding:20px;
                background:#fff3f3;border:2px solid #e74c3c;border-radius:8px;">
        <h2 style="color:#e74c3c;">❌ Erreur de connexion à la base de données</h2>
        <p><strong>Message :</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Vérifie que :</p>
        <ul>
            <li>XAMPP est démarré (Apache + MySQL)</li>
            <li>La base <strong>devmates</strong> existe (crée-la dans phpMyAdmin)</li>
            <li>Les identifiants dans <code>config/db.php</code> sont corrects</li>
        </ul>
    </div>');
}
