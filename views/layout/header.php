<?php
// =============================================
// FICHIER : views/layout/header.php
// RÔLE    : En-tête commune à TOUTES les pages
//           Contient :
//           - Les balises HTML <head> (CSS, CDN)
//           - La barre de navigation Bootstrap
//           - Le badge de messages non lus
// =============================================

// Accéder à la connexion PDO globale (créée dans config/db.php, inclus depuis index.php)
global $pdo;

// Compter les messages non lus pour afficher le badge dans la navbar
$unreadCount = 0;
if (!empty($_SESSION['user_id']) && $pdo) {
    $stmtUnread = $pdo->prepare(
        "SELECT COUNT(*) FROM messages WHERE id_destinataire = ? AND lu = 0"
    );
    $stmtUnread->execute([$_SESSION['user_id']]);
    $unreadCount = (int) $stmtUnread->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Titre de la page (défini dans chaque contrôleur) -->
    <title><?= htmlspecialchars($pageTitle ?? 'DevMates') ?></title>

    <!-- Bootstrap 5 — Framework CSS pour le design responsive -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome — Bibliothèque d'icônes (fas fa-...) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Nos styles personnalisés (par-dessus Bootstrap) -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ========================================================
     BARRE DE NAVIGATION
     Différente selon si l'utilisateur est connecté ou non
======================================================== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <!-- Logo / Nom du site → retour à l'accueil -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-code me-2 text-primary"></i>DevMates
        </a>

        <!-- Bouton "hamburger" pour mobile (réduit la navbar sur petit écran) -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                aria-controls="navbarMenu" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">

                <li class="nav-item">
                    <a class="nav-link px-3" href="index.php?controller=faq">
                        <i class="fas fa-question-circle me-1"></i>FAQ
                    </a>
                </li>

                <?php if (!empty($_SESSION['user_id'])): ?>
                <!-- ===== MENU UTILISATEUR CONNECTÉ ===== -->

                    <li class="nav-item">
                        <a class="nav-link px-3" href="index.php?controller=match&action=list">
                            <i class="fas fa-heart text-danger me-1"></i>Matches
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3" href="index.php?controller=message&action=inbox">
                            <i class="fas fa-envelope me-1"></i>Messages
                            <?php if ($unreadCount > 0): ?>
                                <!-- Badge rouge indiquant les messages non lus -->
                                <span class="badge bg-danger rounded-pill"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link px-3" href="index.php?controller=profile&action=edit">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($_SESSION['user_prenom']) ?>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <span class="badge bg-danger ms-1">Admin</span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-danger btn-sm px-3"
                           href="index.php?controller=auth&action=logout">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </a>
                    </li>

                <?php else: ?>
                <!-- ===== MENU VISITEUR NON CONNECTÉ ===== -->

                    <li class="nav-item">
                        <a class="nav-link px-3" href="index.php?controller=auth&action=login">
                            <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        </a>
                    </li>

                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm px-3"
                           href="index.php?controller=auth&action=register">
                            <i class="fas fa-user-plus me-1"></i>S'inscrire
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<!-- ========================================================
     ZONE DE CONTENU PRINCIPAL
     Le footer.php fermera cette balise <main>
======================================================== -->
<main class="py-4">
