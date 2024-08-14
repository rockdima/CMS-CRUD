<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService {

    function __construct(private UserRepository $UserRepository) {
    }

    /**
     * Login to the system
     * 
     * @param string $user username
     * @param string $password password
     * @return bool
     */
    function login($user, $password): bool {
        return $this->UserRepository->login($user, $password);
    }

    /**
     * Register to the system
     * 
     * @param string $user username
     * @param string $password password
     * @return bool
     */
    function register($user, $password): bool {
        return $this->UserRepository->register($user, $password);
    }
}
