<?php

namespace App\utils\db;

use App\utils\db\DBDriver;

interface DBInterface {
    public function __construct(DBDriver $driver, string $host, string $port, string $db, string $user, string $password);
    public function query(string $query, array $params);
    public function fetch(string $query, array $params): array;
    public function execute(string $query, array $params): bool;
}
