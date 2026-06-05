<?php
// =============================================
// FICHIER : views/home.php
// RÔLE    : Page d'accueil / landing page
//           Présente les fonctionnalités principales
//           de DevMates à un visiteur non connecté.
// =============================================
?>

<!-- ======================
     SECTION HÉRO
====================== -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row align-items-center">

            <!-- Texte principal -->
            <div class="col-lg-6">
                <p class="eyebrow text-uppercase mb-3">Tinder pour développeurs</p>
                <h1 class="display-5 fw-bold mb-4">
                    DevMates Trouve ton développeur idéal
                </h1>
                <p class="lead text-muted mb-4">
                    Le Tinder des développeurs et porteurs de projets.
                    Crée ton profil, découvre des matches pertinents et
                    échange directement pour collaborer sur de vrais projets.
                </p>

                <?php if (empty($_SESSION['user_id'])): ?>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="index.php?controller=auth&action=register"
                           class="btn btn-primary btn-lg px-4 shadow-sm">
                            S'inscrire
                        </a>
                        <a href="index.php?controller=auth&action=login"
                           class="btn btn-outline-dark btn-lg px-4">
                            Se connecter
                        </a>
                    </div>
                <?php else: ?>
                    <a href="index.php?controller=match&action=list"
                       class="btn btn-light btn-lg px-5 shadow-sm">
                        Voir mes matches
                    </a>
                <?php endif; ?>

                <div class="mt-5 hero-stats row gx-3 gy-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <strong>3 étapes</strong>
                            <p class="mb-0">Pour trouver ton prochain dev.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <strong>Matching intelligent</strong>
                            <p class="mb-0">Basé sur les compétences et projets.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte résumé / visuel -->
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="hero-card p-4 shadow-lg border-0">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <p class="mb-1 text-uppercase text-muted small">Nouveau sur DevMates</p>
                            <h3 class="mb-0">  
                                
                            Exemples de profils</h3>
                        </div>
                        <span class="badge rounded-pill bg-primary text-white py-2 px-3">
                            Gratuit</span>
                    </div>

                    <div class="profile-sample mb-4">
                        <div class="profile-avatar">DM</div>
                        <div>
                            <h5 class="mb-1">Alice — Développeuse PHP</h5>
                            <p class="mb-2 text-muted small">PHP / MySQL / APIs / Git</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark">PHP</span>
                                <span class="badge bg-light text-dark">MySQL</span>
                                <span class="badge bg-light text-dark">React</span>
                            </div>
                        </div>
                    </div>

                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-start mb-3">
                            <span class="icon-circle bg-primary text-white me-3">
                                <i class="fas fa-check"></i>
                            </span>
                            <div>
                                <strong>Profil rapide</strong>
                                <p class="mb-0 text-muted">Ajoute tes compétences en quelques clics.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-3">
                            <span class="icon-circle bg-purple text-white me-3">
                                <i class="fas fa-heart"></i>
                            </span>
                            <div>
                                <strong>Matches pertinents</strong>
                                <p class="mb-0 text-muted">Voir les meilleurs profils selon ton projet.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <span class="icon-circle bg-cyan text-white me-3">
                                <i class="fas fa-comments"></i>
                            </span>
                            <div>
                                <strong>Messages privés</strong>
                                <p class="mb-0 text-muted">Échange en direct avec tes futurs collaborateurs.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================
     SECTION COMMENT ÇA MARCHE
====================== -->
<section class="section-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="eyebrow text-uppercase">Comment ça marche ?</span>
            <h2 class="fw-bold mt-3">Trois étapes simples pour rejoindre DevMates</h2>
            <p class="text-muted mx-auto mt-3" style="max-width:680px;">
                Que tu sois développeur ou porteur de projet, DevMates te connecte rapidement
                aux bons profils et te donne un espace pour démarrer ta collaboration.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <article class="feature-card p-4 h-100">
                    <div class="icon-box bg-primary text-white mb-4">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Crée ton profil</h3>
                    <p class="text-muted mb-0">
                        Renseigne ton expérience, tes compétences et le type de projet que tu recherches.
                    </p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card p-4 h-100">
                    <div class="icon-box bg-purple text-white mb-4">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Découvre tes matches</h3>
                    <p class="text-muted mb-0">
                        Parcours des profils compatibles et prends contact avec ceux qui te correspondent.
                    </p>
                </article>
            </div>
            <div class="col-md-4">
                <article class="feature-card p-4 h-100">
                    <div class="icon-box bg-cyan text-white mb-4">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Collabore</h3>
                    <p class="text-muted mb-0">
                        Échange des messages privés et lance ton projet avec un DevMate motivé.
                    </p>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- ======================
     SECTION AVANTAGES
====================== -->
<section class="section-gradient py-5">
    <div class="container">
        <div class="text-center mb-5 text-white">
            <span class="eyebrow text-uppercase">Pourquoi DevMates ?</span>
            <h2 class="fw-bold mt-3">Une plateforme pensée pour les développeurs et porteurs de projet</h2>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="benefit-card p-4 h-100">
                    <div class="mb-4 icon-circle bg-white text-primary">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Open Source &amp; Gratuit</h3>
                    <p class="text-muted mb-0">
                        Projet étudiant qui reste accessible à tous sans frais cachés.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card p-4 h-100">
                    <div class="mb-4 icon-circle bg-white text-purple">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Matching intelligent</h3>
                    <p class="text-muted mb-0">
                        Les compétences et les attentes sont prises en compte pour des résultats plus pertinents.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card p-4 h-100">
                    <div class="mb-4 icon-circle bg-white text-cyan">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Sécurisé</h3>
                    <p class="text-muted mb-0">
                        Mots de passe hashés et requêtes préparées pour protéger les données utilisateurs.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================
     SECTION CTA FINALE
====================== -->
<section class="cta-section py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Prêt à trouver ton DevMate ?</h2>
        <p class="text-muted mb-4">Rejoins la communauté et commence à matcher avec des développeurs motivés.</p>
        <a href="index.php?controller=auth&action=register" class="btn btn-primary btn-lg px-5 shadow-sm">
            Créer mon compte gratuit
        </a>
    </div>
</section>
