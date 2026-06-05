<?php
// =============================================
// FICHIER : views/messages/inbox.php
// RÔLE    : Affiche la boîte de réception
//           et les messages envoyés
//
// VARIABLES disponibles (passées par MessageController) :
//   $received → array  Messages reçus (avec nom de l'expéditeur)
//   $sent     → array  Messages envoyés (avec nom du destinataire)
// =============================================
?>

<div class="container">

    <!-- ===== EN-TÊTE ===== -->
    <div class="mb-4">
        <h2 class="fw-bold mb-1">
            <i class="fas fa-envelope text-primary me-2"></i>Messagerie
        </h2>
        <p class="text-muted mb-0 small">
            Tous les messages reçus ont été marqués comme lus à l'ouverture.
        </p>
    </div>

    <div class="row g-4">

        <!-- ==========================================
             COLONNE GAUCHE : Messages REÇUS
        =========================================== -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">

                <!-- En-tête de la section -->
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-inbox me-2"></i>Reçus
                        <span class="badge bg-white text-primary ms-2">
                            <?= count($received) ?>
                        </span>
                    </h5>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($received)): ?>
                        <!-- État vide -->
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                            Aucun message reçu pour le moment.
                        </div>

                    <?php else: ?>
                        <!-- Liste des messages reçus -->
                        <div class="list-group list-group-flush">

                            <?php foreach ($received as $msg): ?>
                            <div class="list-group-item">

                                <!-- Expéditeur et date -->
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-semibold">
                                        <!-- Petit avatar circulaire de l'expéditeur -->
                                        <span class="badge bg-primary rounded-circle me-1"
                                              style="width:28px;height:28px;display:inline-flex;
                                                     align-items:center;justify-content:center;">
                                            <?= strtoupper(substr($msg['prenom'], 0, 1)) ?>
                                        </span>
                                        <?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?>
                                    </div>
                                    <small class="text-muted ms-2 text-nowrap">
                                        <?= date('d/m/Y', strtotime($msg['date_envoi'])) ?>
                                        <br>
                                        <span class="text-muted">
                                            <?= date('H:i', strtotime($msg['date_envoi'])) ?>
                                        </span>
                                    </small>
                                </div>

                                <!-- Contenu du message -->
                                <p class="mb-2 text-muted small">
                                    <?= nl2br(htmlspecialchars($msg['contenu'])) ?>
                                </p>

                                <!-- Bouton Répondre (pré-remplit le formulaire d'envoi) -->
                                <form method="POST"
                                      action="index.php?controller=message&action=send"
                                      class="mt-1">
                                    <input type="hidden"
                                           name="id_destinataire"
                                           value="<?= (int)$msg['id_expediteur'] ?>">
                                    <div class="input-group input-group-sm">
                                        <input type="text"
                                               name="contenu"
                                               class="form-control"
                                               placeholder="Répondre..."
                                               required>
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-reply"></i>
                                        </button>
                                    </div>
                                </form>

                            </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <!-- ==========================================
             COLONNE DROITE : Messages ENVOYÉS
        =========================================== -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-secondary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-paper-plane me-2"></i>Envoyés
                        <span class="badge bg-white text-secondary ms-2">
                            <?= count($sent) ?>
                        </span>
                    </h5>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($sent)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-paper-plane fa-2x mb-2 d-block opacity-50"></i>
                            Aucun message envoyé pour le moment.<br>
                            <a href="index.php?controller=match&action=list" class="small">
                                Trouver des développeurs à contacter
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="list-group list-group-flush">

                            <?php foreach ($sent as $msg): ?>
                            <div class="list-group-item">

                                <!-- Destinataire et date -->
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-semibold">
                                        <span class="badge bg-secondary rounded-circle me-1"
                                              style="width:28px;height:28px;display:inline-flex;
                                                     align-items:center;justify-content:center;">
                                            <?= strtoupper(substr($msg['prenom'], 0, 1)) ?>
                                        </span>
                                        À : <?= htmlspecialchars($msg['prenom'] . ' ' . $msg['nom']) ?>
                                    </div>
                                    <small class="text-muted ms-2 text-nowrap">
                                        <?= date('d/m/Y', strtotime($msg['date_envoi'])) ?>
                                        <br>
                                        <span class="text-muted">
                                            <?= date('H:i', strtotime($msg['date_envoi'])) ?>
                                        </span>
                                    </small>
                                </div>

                                <!-- Contenu -->
                                <p class="mb-0 text-muted small">
                                    <?= nl2br(htmlspecialchars($msg['contenu'])) ?>
                                </p>

                            </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>

    <!-- ===== LIEN VERS LA PAGE DE MATCHING ===== -->
    <div class="text-center mt-4">
        <a href="index.php?controller=match&action=list"
           class="btn btn-outline-primary">
            <i class="fas fa-heart me-2"></i>Trouver des développeurs à contacter
        </a>
    </div>

</div>
