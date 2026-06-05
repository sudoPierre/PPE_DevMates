<?php
// =============================================
// FICHIER : views/layout/footer.php
// RÔLE    : Pied de page commun à toutes les pages
//           Ferme les balises HTML ouvertes dans
//           header.php et inclut le JS Bootstrap
// =============================================
?>

</main><!-- Fin de <main> ouvert dans header.php -->

<!-- ==========================================
     PIED DE PAGE
========================================== -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">

        <p class="mb-1 fw-bold">
            <i class="fas fa-code me-2 text-primary"></i>DevMates
        </p>

        <p class="text-muted small mb-2">
            Plateforme de matching entre développeurs — Projet BTS SIO SLAM
        </p>

        <!-- Liens de navigation rapide dans le footer -->
        <div class="mb-2">
            <a href="index.php" class="text-white-50 text-decoration-none me-3 small">Accueil</a>
            <a href="index.php?controller=faq" class="text-white-50 text-decoration-none me-3 small">FAQ</a>
            <a href="index.php?controller=legal" class="text-white-50 text-decoration-none me-3 small">Mentions légales</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="index.php?controller=match&action=list"    class="text-white-50 text-decoration-none me-3 small">Matches</a>
                <a href="index.php?controller=message&action=inbox" class="text-white-50 text-decoration-none me-3 small">Messages</a>
                <a href="index.php?controller=profile&action=edit"  class="text-white-50 text-decoration-none small">Mon profil</a>
            <?php else: ?>
                <a href="index.php?controller=auth&action=login"    class="text-white-50 text-decoration-none me-3 small">Connexion</a>
                <a href="index.php?controller=auth&action=register" class="text-white-50 text-decoration-none small">Inscription</a>
            <?php endif; ?>
        </div>


    </div>
</footer>

<!-- Bootstrap 5 JS (nécessaire pour le menu hamburger sur mobile, les modales, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
