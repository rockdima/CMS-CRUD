<?php

namespace App\Repositories;

use App\utils\db\DBInterface;

class UserRepository {
    function __construct(private DBInterface $db) {
    }

     /**
     * Login to the system
     * 
     * @param string $user username
     * @param string $password password
     * @return bool
     */
    function login(string $user, string $password): bool {
        $query = "SELECT password FROM users 
                    WHERE 
                    username=:username";
        $pass = $this->db->fetch($query, [':username' => $user]);
        if(count($pass)) {
            return password_verify($password, $pass[0]['password']);
        }
        return false;
    }

    /**
     * Register to the system
     * 
     * @param string $user username
     * @param string $password password
     * @return bool
     */
    function register($user, $password): bool {
        $query = "INSERT INTO users (username, password)
                    VALUES(:username, :password)";
        return $this->db->execute($query, [':username' => $user, ':password' => $this->create_hash($password)]);
    }

    /**
     * Create password hash
     * 
     * @param string $pass password
     * @return string
     */
    private function create_hash(string $pass): string {
        return password_hash($pass, PASSWORD_BCRYPT);
    }
}
