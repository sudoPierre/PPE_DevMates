<?php
// =============================================
// FICHIER : controllers/StaticController.php
// RÔLE    : Gère les pages statiques du site
// =============================================

class StaticController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function legal() {
        $pageTitle = 'Mentions légales — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/static/legal.php';
        include 'views/layout/footer.php';
    }

    public function faq() {
        $pageTitle = 'FAQ — DevMates';
        $pdo = $this->pdo;
        include 'views/layout/header.php';
        include 'views/static/faq.php';
        include 'views/layout/footer.php';
    }
}
