<?php
// =============================================
// FICHIER : views/auth/login.php
// RÔLE    : Formulaire de connexion
//
// VARIABLES disponibles (passées par AuthController) :
//   $error  → string  Message d'erreur (vide si OK)
// =============================================
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5">

            <div class="card border-0 shadow mt-3">
                <div class="card-body p-5">

                    <!-- Titre -->
                    <div class="text-center mb-4">
                        <i class="fas fa-sign-in-alt fa-2x text-primary mb-2 d-block"></i>
                        <h2 class="fw-bold">Connexion</h2>
                        <p class="text-muted small">Accède à tes matches et messages</p>
                    </div>

                    <!-- ===== ALERTE ERREUR ===== -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                            <!-- htmlspecialchars() empêche les injections XSS -->
                        </div>
                    <?php endif; ?>

                    <!-- ===== FORMULAIRE DE CONNEXION ===== -->
                    <!-- action → vers AuthController::login() via POST -->
                    <form method="POST" action="index.php?controller=auth&action=login"
                          novalidate>

                        <!-- Champ Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>Adresse email
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control form-control-lg"
                                   placeholder="ton@email.fr"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   autocomplete="email"
                                   required>
                        </div>

                        <!-- Champ Mot de passe -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>Mot de passe
                            </label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control form-control-lg"
                                   placeholder="••••••••"
                                   autocomplete="current-password"
                                   required>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </div>

                    </form>

                    <!-- Lien vers l'inscription -->
                    <hr class="my-4">
                    <p class="text-center mb-0 text-muted">
                        Pas encore de compte ?
                        <a href="index.php?controller=auth&action=register" class="fw-semibold">
                            Créer un compte
                        </a>
                    </p>

                    <!-- Compte de démo pour le professeur -->
                    <div class="mt-3 p-3 bg-light rounded small text-center text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Compte démo :</strong> alice@devmates.fr / devmates123
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
