<?php

declare(strict_types=1);

/**
 * Front Controller — point d'entrée unique de l'API REST DevMates
 * Toutes les requêtes HTTP passent par ce fichier (cf. .htaccess)
 */

// Autoloader PSR-4 maison (sans Composer) — mappe namespace\ClassName → namespace/ClassName.php
spl_autoload_register(static function (string $class): void {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

use Router\Router;
use Controllers\AuthController;
use Controllers\ProfileController;
use Controllers\MatchController;
use Controllers\MessageController;
use Controllers\AdminController;

// En-têtes de réponse globaux
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$router = new Router();

// ── Authentification ───────────────────────────────────────────
$router->post('/api/auth/register', fn() => (new AuthController())->register());
$router->post('/api/auth/login',    fn() => (new AuthController())->login());

// ── Profils ────────────────────────────────────────────────────
$router->get('/api/profile',      fn()         => (new ProfileController())->getMyProfile());
$router->put('/api/profile',      fn()         => (new ProfileController())->updateMyProfile());
$router->get('/api/profile/:id',  fn(string $id) => (new ProfileController())->getProfile($id));

// ── Matching ───────────────────────────────────────────────────
$router->get('/api/match/candidates', fn()  => (new MatchController())->getCandidates());
$router->post('/api/match/action',    fn()  => (new MatchController())->recordAction());
$router->get('/api/match',            fn()  => (new MatchController())->getMatches());

// ── Messagerie (Short Polling via ?since=) ─────────────────────
$router->get('/api/messages/:matchId',
    fn(string $id) => (new MessageController())->getMessages($id));
$router->post('/api/messages/:matchId',
    fn(string $id) => (new MessageController())->sendMessage($id));

// ── Administration (rôle admin requis) ────────────────────────
$router->get('/api/admin/users',             fn()           => (new AdminController())->getUsers());
$router->put('/api/admin/users/:id',         fn(string $id) => (new AdminController())->updateUser($id));
$router->delete('/api/admin/users/:id',      fn(string $id) => (new AdminController())->deleteUser($id));
$router->post('/api/admin/users/:id/ban',    fn(string $id) => (new AdminController())->banUser($id));

$router->dispatch();
