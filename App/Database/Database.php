<?php declare(strict_types = 1);

namespace App\Database;

use PDO;
use PDOException;

class Database {

    public $pdo;

    public function __construct(string $db, string $username, string $password, string $host = '127.0.0.1', int $port = 3306, array $options = []) {
        $default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];

        $options = array_replace($default_options, $options);
        // $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
        $dsn = "mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=$db;charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
			// var_dump($e);
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function run($sql, $args = NULL) {
        if (!$args) {
            return $this->pdo->query($sql);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt;
    }

    public function lastInsertId(): int {
        return $this->pdo->lastInsertId();
    }

}

