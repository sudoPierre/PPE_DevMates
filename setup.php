<?php
// =============================================
// FICHIER : setup.php
// RÔLE    : Script d'initialisation de la BDD
//           Crée les tables ET insère les
//           utilisateurs de test avec de VRAIS
//           hash de mots de passe PHP
// USAGE   : Ouvrir http://localhost/devmates/setup.php
//           UNE SEULE FOIS, puis supprimer ce fichier !
// =============================================

require_once 'config/db.php';

echo '<style>body{font-family:Arial;max-width:700px;margin:40px auto;padding:20px;}
      .ok{color:green;} .err{color:red;} pre{background:#f4f4f4;padding:10px;border-radius:5px;}</style>';
echo '<h1>🔧 Setup DevMates</h1>';

// Mot de passe commun pour tous les comptes de test
$password = 'devmates123';
$hash     = password_hash($password, PASSWORD_DEFAULT);

// Tableau des utilisateurs à insérer
$users = [
    [
        'nom'         => 'Martin',
        'prenom'      => 'Alice',
        'email'       => 'alice@devmates.fr',
        'role'        => 'dev',
        'competences' => 'PHP,Python,MySQL,Git',
        'bio'         => "Passionnée de développement web full-stack. J'adore créer des APIs REST en PHP !",
    ],
    [
        'nom'         => 'Dupont',
        'prenom'      => 'Bob',
        'email'       => 'bob@devmates.fr',
        'role'        => 'dev',
        'competences' => 'JavaScript,React,NodeJS,MongoDB',
        'bio'         => "Développeur frontend React. Je cherche des collabs sur des projets web modernes.",
    ],
    [
        'nom'         => 'Bernard',
        'prenom'      => 'Clara',
        'email'       => 'clara@devmates.fr',
        'role'        => 'dev',
        'competences' => 'PHP,Java,Docker,MySQL,Linux',
        'bio'         => "Fan de DevOps et backend. J'aime automatiser tout ce qui peut l'être !",
    ],
    [
        'nom'         => 'Admin',
        'prenom'      => 'Super',
        'email'       => 'admin@devmates.fr',
        'role'        => 'admin',
        'competences' => '',
        'bio'         => 'Administrateur de la plateforme DevMates.',
    ],
];

echo "<h2>1. Création des utilisateurs de test</h2>";
echo "<p>Mot de passe pour tous les comptes : <strong>$password</strong></p>";

// Vider la table avant l'insertion (évite les doublons)
$pdo->exec("DELETE FROM messages");
$pdo->exec("DELETE FROM matches");
$pdo->exec("DELETE FROM users");
echo "<p class='ok'>✓ Tables vidées</p>";

// Insérer chaque utilisateur avec un vrai hash
$stmt = $pdo->prepare(
    "INSERT INTO users (nom, prenom, email, mot_de_passe, role, competences, bio)
     VALUES (:nom, :prenom, :email, :mdp, :role, :competences, :bio)"
);

foreach ($users as $user) {
    $stmt->execute([
        ':nom'         => $user['nom'],
        ':prenom'      => $user['prenom'],
        ':email'       => $user['email'],
        ':mdp'         => $hash,   // Même hash pour tous (test uniquement)
        ':role'        => $user['role'],
        ':competences' => $user['competences'],
        ':bio'         => $user['bio'],
    ]);
    echo "<p class='ok'>✓ Utilisateur créé : {$user['prenom']} {$user['nom']} ({$user['email']})</p>";
}

echo "<h2>2. Création des messages de test</h2>";

// Récupérer les IDs des utilisateurs créés
$alice = $pdo->query("SELECT id FROM users WHERE email = 'alice@devmates.fr'")->fetchColumn();
$bob   = $pdo->query("SELECT id FROM users WHERE email = 'bob@devmates.fr'")->fetchColumn();

// Insérer des messages de test
$stmtMsg = $pdo->prepare(
    "INSERT INTO messages (id_expediteur, id_destinataire, contenu) VALUES (?, ?, ?)"
);
$stmtMsg->execute([$alice, $bob, "Salut Bob ! J'ai vu que tu maîtrises React. Je cherche un partenaire pour un projet web !"]);
$stmtMsg->execute([$bob, $alice, "Salut Alice ! Super, dis-moi en plus sur ton projet, je suis intéressé !"]);

echo "<p class='ok'>✓ Messages de test créés</p>";

echo "<h2>✅ Setup terminé !</h2>";
echo "<p>Tu peux maintenant :</p><ul>
      <li>Te connecter avec <strong>alice@devmates.fr</strong> / <strong>devmates123</strong></li>
      <li>Te connecter avec <strong>bob@devmates.fr</strong> / <strong>devmates123</strong></li>
      <li>Te connecter avec <strong>admin@devmates.fr</strong> / <strong>devmates123</strong></li>
      </ul>";
echo "<p><strong>⚠️ Supprime ce fichier (setup.php) en production !</strong></p>";
echo '<a href="index.php" style="background:#4f46e5;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;">
      → Aller sur DevMates</a>';
