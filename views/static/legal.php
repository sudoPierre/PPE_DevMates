<?php
// =============================================
// FICHIER : views/static/legal.php
// RÔLE    : Page des mentions légales
// =============================================
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">

                    <div class="text-center mb-4">
                        <h1 class="fw-bold">Mentions Légales</h1>
                        <p class="text-muted">Mise à jour : Janvier 2026 &middot; Droit français applicable</p>
                    </div>

                    <!-- Résumé -->
                    <table class="table table-bordered mb-5">
                        <tbody>
                            <tr><th>Éditeur</th><td>DevCode SAS</td></tr>
                            <tr><th>Directeur de publication</th><td>Alexandre Martin</td></tr>
                            <tr><th>Siège social</th><td>42 avenue de l'Innovation, 75011 Paris</td></tr>
                            <tr><th>Email</th><td><a href="mailto:contact@devcode.fr">contact@devcode.fr</a></td></tr>
                            <tr><th>Loi applicable</th><td>Droit français</td></tr>
                        </tbody>
                    </table>

                    <!-- 1. Éditeur -->
                    <h2 class="h4 fw-bold mt-4 mb-3">1. Éditeur du site</h2>
                    <table class="table table-bordered mb-5">
                        <thead class="table-light">
                            <tr><th>Champ</th><th>Information</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Société</td><td>DevCode SAS</td></tr>
                            <tr><td>Forme juridique</td><td>Société par Actions Simplifiée</td></tr>
                            <tr><td>Capital social</td><td>10 000 €</td></tr>
                            <tr><td>Siège social</td><td>42 avenue de l'Innovation, 75011 Paris</td></tr>
                            <tr><td>Email</td><td><a href="mailto:contact@devcode.fr">contact@devcode.fr</a></td></tr>
                            <tr><td>Téléphone</td><td>+33 1 42 00 00 00</td></tr>
                            <tr><td>SIRET</td><td>123 456 789 00010</td></tr>
                            <tr><td>RCS</td><td>Paris B 123 456 789</td></tr>
                        </tbody>
                    </table>

                    <!-- 2. Hébergeur -->
                    <h2 class="h4 fw-bold mt-4 mb-3">2. Hébergeur</h2>
                    <table class="table table-bordered mb-5">
                        <thead class="table-light">
                            <tr><th>Champ</th><th>Information</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Hébergeur</td><td>Établissement scolaire EFREI (infrastructure interne)</td></tr>
                            <tr><td>Adresse</td><td>30-32 Av. de la République, 94800 Villejuif</td></tr>
                            <tr><td>Site web</td><td><a href="https://www.efrei.fr" target="_blank">https://www.efrei.fr</a></td></tr>
                            <tr><td>Environnement</td><td>Proxmox VE — Virtualisation sur switch école</td></tr>
                        </tbody>
                    </table>

                    <!-- 3. Directeur de la publication -->
                    <h2 class="h4 fw-bold mt-4 mb-3">3. Directeur de la publication</h2>
                    <p>Le directeur de la publication est <strong>Monsieur Alexandre Martin</strong>, Président de DevCode SAS, joignable à l'adresse : <a href="mailto:alexandre.martin@devcode.fr">alexandre.martin@devcode.fr</a></p>

                    <!-- 4. Propriété intellectuelle -->
                    <h2 class="h4 fw-bold mt-4 mb-3">4. Propriété intellectuelle</h2>
                    <p>L'ensemble des contenus présents sur le site DevMates incluant, sans s'y limiter, les textes, graphismes, logos, icônes, images, clips audio, téléchargements numériques et compilations de données est la propriété exclusive de DevCode SAS et est protégé par les lois françaises et internationales relatives au droit d'auteur et à la propriété intellectuelle.</p>
                    <p>Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie des éléments du site, quel que soit le moyen ou le procédé utilisé, est interdite sans l'autorisation écrite préalable de DevCode SAS.</p>

                    <!-- 5. RGPD -->
                    <h2 class="h4 fw-bold mt-4 mb-3">5. Protection des données personnelles (RGPD)</h2>
                    <p>Conformément au Règlement Général sur la Protection des Données (RGPD – UE 2016/679) et à la loi Informatique et Libertés, DevCode SAS traite les données personnelles suivantes dans le cadre du service DevMates :</p>
                    <table class="table table-bordered mb-3">
                        <thead class="table-light">
                            <tr><th>Catégorie</th><th>Détail</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Données collectées</td>
                                <td>Nom, prénom, adresse email, mot de passe (hashé), compétences techniques, biographie, messages échangés.</td>
                            </tr>
                            <tr>
                                <td>Finalité</td>
                                <td>Création et gestion du compte utilisateur, mise en relation entre développeurs, messagerie intégrée.</td>
                            </tr>
                            <tr>
                                <td>Durée de conservation</td>
                                <td>Les données sont conservées pendant toute la durée d'activité du compte, puis supprimées sous 30 jours après résiliation.</td>
                            </tr>
                            <tr>
                                <td>Vos droits</td>
                                <td>Droit d'accès, de rectification, d'effacement, de limitation, de portabilité et d'opposition. Demande à : <a href="mailto:rgpd@devcode.fr">rgpd@devcode.fr</a></td>
                            </tr>
                            <tr>
                                <td>DPO</td>
                                <td>Délégué à la protection des données : <a href="mailto:dpo@devcode.fr">dpo@devcode.fr</a></td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- 6. Cookies -->
                    <h2 class="h4 fw-bold mt-4 mb-3">6. Cookies</h2>
                    <p>DevMates utilise uniquement des cookies de session strictement nécessaires au fonctionnement du site (maintien de la connexion de l'utilisateur). Aucun cookie publicitaire ou de traçage tiers n'est déposé sur votre appareil.</p>
                    <p>Vous pouvez désactiver les cookies dans les paramètres de votre navigateur, ce qui entraînera l'impossibilité de vous connecter à votre compte.</p>

                    <!-- 7. Limitation de responsabilité -->
                    <h2 class="h4 fw-bold mt-4 mb-3">7. Limitation de responsabilité</h2>
                    <p>DevCode SAS s'efforce de maintenir les informations diffusées sur DevMates aussi précises et à jour que possible. Cependant, DevCode SAS ne peut garantir l'exactitude, la complétude ou l'actualité des informations publiées par les utilisateurs sur la plateforme.</p>
                    <p>DevCode SAS ne saurait être tenu responsable des échanges, propos ou contenus partagés entre utilisateurs via la messagerie intégrée, ni des conséquences découlant de collaborations initiées sur la plateforme.</p>
                    <p>Le site peut être temporairement indisponible pour des raisons de maintenance technique. DevCode SAS ne peut être tenu responsable de ces interruptions de service.</p>

                    <!-- 8. Loi applicable -->
                    <h2 class="h4 fw-bold mt-4 mb-3">8. Loi applicable et juridiction compétente</h2>
                    <p>Les présentes mentions légales sont régies par le droit français. En cas de litige relatif à l'interprétation ou à l'exécution des présentes, et à défaut de résolution amiable, les tribunaux de Paris seront seuls compétents.</p>
                    <table class="table table-bordered mb-3">
                        <thead class="table-light">
                            <tr><th>Champ</th><th>Détail</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Droit applicable</td><td>Droit français</td></tr>
                            <tr><td>Tribunal compétent</td><td>Tribunal de Commerce de Paris</td></tr>
                            <tr><td>Langue</td><td>Français</td></tr>
                            <tr><td>Mise à jour</td><td>Janvier 2026</td></tr>
                        </tbody>
                    </table>

                    <p class="text-muted text-end mt-4"><small>BTS SIO SLAM — Session 2026</small></p>

                </div>
            </div>
        </div>
    </div>
</div>
