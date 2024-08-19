<?php

namespace App\utils\db;

enum DBDriver: string {
    case MYSQL='mysql';
    case POSTGRESQL='pgsql';
}
