<?php
// =============================================
// FICHIER : views/profile/edit.php
// RÔLE    : Affiche et permet de modifier
//           le profil de l'utilisateur connecté
//
// VARIABLES disponibles (passées par ProfileController) :
//   $user    → array   Données actuelles de l'utilisateur
//   $error   → string  Message d'erreur
//   $success → string  Message de succès
// =============================================
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <!-- ===== CARTE DU PROFIL ===== -->
            <div class="card border-0 shadow mt-3">

                <!-- En-tête coloré de la carte -->
                <div class="card-header bg-primary text-white py-3 rounded-top">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-user-edit me-2"></i>Mon profil
                    </h4>
                </div>

                <div class="card-body p-4 p-md-5">

                    <!-- ===== RÉSUMÉ DU PROFIL (avatar + nom + infos) ===== -->
                    <div class="text-center mb-4 pb-3 border-bottom">

                        <!-- Avatar avec les initiales du prénom et du nom -->
                        <div class="avatar-lg mb-3">
                            <?= strtoupper(
                                substr($user['prenom'], 0, 1) .
                                substr($user['nom'],    0, 1)
                            ) ?>
                        </div>

                        <h4 class="fw-bold mb-1">
                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                        </h4>

                        <!-- Badge selon le rôle -->
                        <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?> mb-2">
                            <?= $user['role'] === 'admin' ? '🔧 Admin' : '👨‍💻 Développeur' ?>
                        </span>

                        <p class="text-muted small mb-0">
                            <i class="fas fa-calendar me-1"></i>
                            Membre depuis le <?= date('d/m/Y', strtotime($user['date_inscription'])) ?>
                        </p>
                    </div>

                    <!-- ===== ALERTES SUCCÈS / ERREUR ===== -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- ===== FORMULAIRE DE MODIFICATION ===== -->
                    <form method="POST" action="index.php?controller=profile&action=edit">

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-1 text-muted"></i>Email
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   value="<?= htmlspecialchars($user['email']) ?>"
                                   required>
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
                                   value="<?= htmlspecialchars($user['competences'] ?? '') ?>"
                                   placeholder="PHP, Python, MySQL, React...">
                            <div class="form-text">Séparées par des virgules</div>
                        </div>

                        <!-- Affichage des compétences actuelles sous forme de badges -->
                        <?php
                        $skills = array_filter(array_map('trim', explode(',', $user['competences'] ?? '')));
                        if (!empty($skills)):
                        ?>
                        <div class="mb-3">
                            <p class="small text-muted mb-1">Compétences actuelles :</p>
                            <?php foreach ($skills as $skill): ?>
                                <span class="badge bg-primary me-1 mb-1">
                                    <?= htmlspecialchars($skill) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label for="bio" class="form-label fw-semibold">
                                <i class="fas fa-pen me-1 text-muted"></i>Bio / Présentation
                            </label>
                            <textarea name="bio"
                                      id="bio"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Décris-toi, tes projets, ce que tu cherches..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>

                        <!-- Bouton de sauvegarde -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- Liens de navigation -->
            <div class="d-flex gap-2 justify-content-center mt-3 mb-4">
                <a href="index.php?controller=match&action=list"
                   class="btn btn-outline-primary">
                    <i class="fas fa-heart me-2"></i>Voir mes matches
                </a>
                <a href="index.php?controller=message&action=inbox"
                   class="btn btn-outline-secondary">
                    <i class="fas fa-envelope me-2"></i>Messagerie
                </a>
            </div>

        </div>
    </div>
</div>
