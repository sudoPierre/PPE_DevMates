<?php
// =============================================
// FICHIER : views/static/faq.php
// RÔLE    : Page FAQ du site
// =============================================
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4">FAQ — Comment ça marche ?</h1>

            <div class="accordion" id="faqAccordion">

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                            Est-ce gratuit ?
                        </button>
                    </h2>
                    <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, DevMates est présenté comme un projet étudiant et l'accès à la plateforme est entièrement gratuit. Il n'y a pas de frais pour s'inscrire ou utiliser les fonctionnalités.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                            Comment fonctionne le matching ?
                        </button>
                    </h2>
                    <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Les profils sont comparés sur la base des compétences, des centres d'intérêt et des préférences. Le système propose ensuite des développeurs compatibles pour faciliter des collaborations ou des projets communs.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                            Est-ce que les messages sont privés ?
                        </button>
                    </h2>
                    <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oui, chaque conversation est privée et accessible uniquement par les utilisateurs concernés. Les messages sont stockés dans la base de données et affichés uniquement à l'expéditeur et au destinataire.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqHeading4">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                            Comment sont calculés les scores ?
                        </button>
                    </h2>
                    <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Les scores sont basés sur la compatibilité des compétences, la disponibilité déclarée et les affinités entre profils. Cela permet de proposer des correspondances pertinentes et adaptées aux besoins des développeurs.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
