<?php
// =============================================
// FICHIER : views/auth/register.php
// RÔLE    : Formulaire d'inscription
//           Collecte les informations du nouveau
//           développeur pour créer son compte
//
// VARIABLES disponibles (passées par AuthController) :
//   $error  → string  Message d'erreur (vide si OK)
// =============================================
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-11 col-md-8 col-lg-7">

            <div class="card border-0 shadow mt-3">
                <div class="card-body p-5">

                    <!-- Titre -->
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-2x text-primary mb-2 d-block"></i>
                        <h2 class="fw-bold">Créer mon compte</h2>
                        <p class="text-muted small">Rejoins la communauté DevMates</p>
                    </div>

                    <!-- ===== ALERTE ERREUR ===== -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- ===== FORMULAIRE D'INSCRIPTION ===== -->
                    <form method="POST" action="index.php?controller=auth&action=register"
                          novalidate>

                        <!-- Nom et Prénom sur la même ligne (grille Bootstrap) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label fw-semibold">
                                    Prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="prenom"
                                       id="prenom"
                                       class="form-control"
                                       placeholder="Alice"
                                       value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label fw-semibold">
                                    Nom <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="nom"
                                       id="nom"
                                       class="form-control"
                                       placeholder="Martin"
                                       value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   placeholder="alice@exemple.fr"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   autocomplete="email"
                                   required>
                        </div>

                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>
                                Mot de passe <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   placeholder="minimum 6 caractères"
                                   autocomplete="new-password"
                                   required>
                            <div class="form-text">Minimum 6 caractères</div>
                        </div>

                        <!-- Rôle -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-muted"></i>Rôle
                            </label>
                            <select name="role" id="role" class="form-select">
                                <option value="dev"
                                    <?= (($_POST['role'] ?? 'dev') === 'dev') ? 'selected' : '' ?>>
                                    👨‍💻 Développeur
                                </option>
                                <option value="admin"
                                    <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                                    🔧 Administrateur
                                </option>
                            </select>
                        </div>

                        <!-- Compétences -->
                        <div class="mb-3">
                            <label for="competences" class="form-label fw-semibold">
                                <i class="fas fa-code me-1 text-muted"></i>Compétences
                            </label>
                            <input type="text"
                                   name="competences"
                                   id="competences"
                                   class="form-control"
                                   placeholder="PHP, Python, JavaScript, MySQL, Docker..."
                                   value="<?= htmlspecialchars($_POST['competences'] ?? '') ?>">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Sépare tes compétences par des virgules.
                                Exemple : <code>PHP,React,MySQL</code>
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label for="bio" class="form-label fw-semibold">
                                <i class="fas fa-pen me-1 text-muted"></i>Présentation
                            </label>
                            <textarea name="bio"
                                      id="bio"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Parle un peu de toi, de tes projets, de ce que tu cherches..."><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-rocket me-2"></i>Créer mon compte
                            </button>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            <span class="text-danger">*</span> Champs obligatoires
                        </p>

                    </form>

                    <!-- Lien vers la connexion -->
                    <hr class="my-4">
                    <p class="text-center mb-0 text-muted">
                        Déjà un compte ?
                        <a href="index.php?controller=auth&action=login" class="fw-semibold">
                            Se connecter
                        </a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</div>
