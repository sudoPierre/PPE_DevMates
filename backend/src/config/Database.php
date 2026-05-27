<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;

/**
 * Connexion PDO à la base de données MySQL — patron Singleton
 * Évite les connexions multiples au cours d'une même requête HTTP
 */
class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host     = $_ENV['DB_HOST']     ?? getenv('DB_HOST')     ?: 'db';
        $port     = $_ENV['DB_PORT']     ?? getenv('DB_PORT')     ?: '3306';
        $dbname   = $_ENV['DB_NAME']     ?? getenv('DB_NAME')     ?: 'devmates';
        $user     = $_ENV['DB_USER']     ?? getenv('DB_USER')     ?: 'devmates_user';
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(503);
            echo json_encode(['erreur' => 'Connexion à la base de données impossible. Veuillez réessayer.']);
            exit();
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
