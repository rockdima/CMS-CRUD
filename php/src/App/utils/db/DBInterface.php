<?php

namespace App\utils\db;

interface DBInterface {
    public function connect();
    public function query(string $query, array $params);
    public function fetch(string $query, array $params): array;
    public function execute(string $query, array $params): bool;
}
