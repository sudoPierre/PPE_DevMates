<?php
// =============================================
// FICHIER : views/match/list.php
// RÔLE    : Affiche la liste des développeurs
//           triés par score de compatibilité
//           décroissant (le plus compatible en 1er)
//
// VARIABLES disponibles (passées par MatchController) :
//   $matches → array  Tableau de développeurs avec leur score
//                     Chaque entrée contient :
//                       - Toutes les colonnes de la table users
//                       - score        : int (0-100)
//                       - commonSkills : array des compétences communes
//                       - allSkills    : array de toutes leurs compétences
// =============================================
?>

<div class="container">

    <!-- ===== EN-TÊTE DE LA PAGE ===== -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fas fa-heart text-danger me-2"></i>Mes DevMates
            </h2>
            <p class="text-muted mb-0 small">
                Développeurs triés par compatibilité avec tes compétences
            </p>
        </div>
        <div>
            <span class="badge bg-secondary fs-6 px-3 py-2">
                <?= count($matches) ?> développeur<?= count($matches) > 1 ? 's' : '' ?>
            </span>
        </div>
    </div>

    <?php if (empty($matches)): ?>
    <!-- ===== ÉTAT VIDE : aucun autre développeur ===== -->
    <div class="text-center py-5">
        <i class="fas fa-user-slash fa-4x text-muted mb-3 d-block"></i>
        <h4 class="text-muted">Aucun développeur disponible pour le moment</h4>
        <p class="text-muted">
            Invite tes amis à rejoindre DevMates pour voir des matches !
        </p>
        <a href="index.php?controller=profile&action=edit" class="btn btn-primary">
            <i class="fas fa-code me-2"></i>Ajouter mes compétences
        </a>
    </div>

    <?php else: ?>
    <!-- ===== GRILLE DES CARTES DE DÉVELOPPEURS ===== -->
    <div class="row g-4">

        <?php foreach ($matches as $dev): ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">

                <!-- EN-TÊTE : Score de compatibilité (couleur selon le score) -->
                <?php
                // Choisir la couleur de l'en-tête selon le score
                if ($dev['score'] >= 60)     $couleur = 'success';   // Vert  >= 60%
                elseif ($dev['score'] >= 30) $couleur = 'warning';   // Jaune >= 30%
                else                         $couleur = 'secondary'; // Gris   < 30%
                ?>
                <div class="card-header bg-<?= $couleur ?> text-white text-center py-3">
                    <div class="score-number"><?= $dev['score'] ?>%</div>
                    <small class="opacity-75">compatibilité</small>
                </div>

                <div class="card-body d-flex flex-column">

                    <!-- Avatar et identité -->
                    <div class="text-center mb-3">
                        <div class="avatar-sm mx-auto mb-2">
                            <?= strtoupper(
                                substr($dev['prenom'], 0, 1) .
                                substr($dev['nom'],    0, 1)
                            ) ?>
                        </div>
                        <h5 class="fw-bold mb-1">
                            <?= htmlspecialchars($dev['prenom'] . ' ' . $dev['nom']) ?>
                        </h5>
                        <span class="badge bg-primary">
                            <?= htmlspecialchars($dev['role']) ?>
                        </span>
                    </div>

                    <!-- Bio (tronquée à 90 caractères) -->
                    <?php if (!empty($dev['bio'])): ?>
                    <p class="text-muted small text-center mb-3">
                        <?= htmlspecialchars(mb_substr($dev['bio'], 0, 90)) ?>
                        <?= mb_strlen($dev['bio']) > 90 ? '…' : '' ?>
                    </p>
                    <?php endif; ?>

                    <!-- Compétences EN COMMUN -->
                    <?php if (!empty($dev['commonSkills'])): ?>
                    <div class="mb-2">
                        <p class="small fw-bold mb-1 text-success">
                            <i class="fas fa-check-circle me-1"></i>En commun :
                        </p>
                        <?php foreach ($dev['commonSkills'] as $skill): ?>
                            <span class="badge bg-success me-1 mb-1">
                                <?= htmlspecialchars($skill) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- TOUTES ses compétences -->
                    <?php if (!empty($dev['allSkills'])): ?>
                    <div class="mb-3">
                        <p class="small fw-bold mb-1 text-muted">
                            <i class="fas fa-code me-1"></i>Ses compétences :
                        </p>
                        <?php foreach ($dev['allSkills'] as $skill): ?>
                            <?php
                            // Mettre en évidence les compétences communes
                            $isCommon = in_array($skill, $dev['commonSkills']);
                            $badgeClass = $isCommon ? 'bg-success' : 'bg-light text-dark border';
                            ?>
                            <span class="badge <?= $badgeClass ?> me-1 mb-1">
                                <?= htmlspecialchars($skill) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Espacement flexible pour pousser le formulaire en bas -->
                    <div class="flex-grow-1"></div>

                    <!-- ===== FORMULAIRE D'ENVOI DE MESSAGE RAPIDE ===== -->
                    <div class="pt-3 border-top mt-2">
                        <p class="small text-muted mb-2">
                            <i class="fas fa-paper-plane me-1"></i>Envoyer un message :
                        </p>
                        <form method="POST"
                              action="index.php?controller=message&action=send">

                            <!-- ID du destinataire (caché, non visible dans le formulaire) -->
                            <input type="hidden"
                                   name="id_destinataire"
                                   value="<?= (int)$dev['id'] ?>">

                            <div class="input-group input-group-sm">
                                <input type="text"
                                       name="contenu"
                                       class="form-control"
                                       placeholder="Salut ! Je voudrais collaborer..."
                                       maxlength="500"
                                       required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
    <?php endif; ?>

    <!-- Lien vers le profil pour améliorer son score -->
    <div class="text-center mt-5">
        <p class="text-muted small">
            <i class="fas fa-lightbulb text-warning me-1"></i>
            Plus tu ajoutes de compétences dans ton profil, mieux tu seras matché !
        </p>
        <a href="index.php?controller=profile&action=edit"
           class="btn btn-outline-primary btn-sm">
            <i class="fas fa-edit me-1"></i>Modifier mes compétences
        </a>
    </div>

</div>
