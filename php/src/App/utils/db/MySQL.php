<?php

namespace App\utils\db;

use PDO;
use Exception;
use PDOStatement;

class MySQL implements DBInterface {

    private $connection;

    function __construct(private $host, private $db, private $user, private $password) {
        $this->connect();
    }

    function connect() {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db}", $this->user, $this->password, $options);
    }

    function query(string $query, array $params) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    function fetch(string $query, array $params = []): array {
        try{
            return $this->query($query, $params)->fetchAll();
        } catch(Exception $e) {
            return [];
        }
    }

    public function execute($sql, $params = []): bool {
        try{
            return $this->query($sql, $params)->rowCount() > 0;
        } catch(Exception $e) {
            return false;
        }
    }
}
