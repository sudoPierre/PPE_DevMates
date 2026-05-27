<?php

declare(strict_types=1);

namespace Router;

/**
 * Routeur HTTP minimaliste pour l'API REST DevMates
 * Associe les méthodes HTTP + chemins URI aux callbacks contrôleurs
 * Supporte les paramètres dynamiques via la syntaxe /:param
 */
class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put(string $path, callable $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable $handler): void
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    /**
     * Résout la requête entrante et appelle le contrôleur correspondant
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        // Réponse immédiate aux requêtes de pré-vol CORS
        if ($method === 'OPTIONS') {
            http_response_code(204);
            exit();
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = $this->buildRegex($pattern);
            if (preg_match($regex, $uri, $matches)) {
                array_shift($matches); // supprime la correspondance complète
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['erreur' => "Route introuvable : {$method} {$uri}"]);
    }

    /**
     * Convertit un pattern de route en regex
     * Ex : /api/users/:id  →  #^/api/users/([^/]+)$#
     */
    private function buildRegex(string $pattern): string
    {
        $regex = preg_replace('#/:([^/]+)#', '/([^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }
}
