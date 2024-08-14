<?php

namespace App\Controllers;

use App\Services\UserService;
use Laminas\Diactoros\Response\JsonResponse;
use App\Middlewares\JWTAuth;
use Valitron\Validator;

class UserController {

    private $body;

    function __construct(private UserService $userService, private JWTAuth $jwtAuth, private Validator $validator) {
        $this->body = $_POST;
    }

    /**
     * Login to the system
     */
    public function login() {

        // validate fields
        $this->validator->mapFieldsRules([
            'username' => ['required', ['lengthMin', 5], 'alphaNum'],
            'password' => ['required', ['lengthMin', 5]]
        ]);
        $this->validator = $this->validator->withData($this->body);

        if (!$this->validator->validate()) {
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Validation failed',
                'data'      => $this->validator->errors()
            ], 200);
        }

        // if passed create JWT
        if ($this->userService->login($this->body['username'], $this->body['password'])) {
            $jwt = $this->jwtAuth->create($this->body['username']);
            return new JsonResponse([
                'status'    => 'success',
                'msg'       => 'Success',
                'data'      => [
                    'token' => $jwt,
                    'redirect' => '/customers.html'
                ]
            ], 200);
        }

        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'Auth Failed',
            'data'      => []
        ], 401);
    }

    /**
     * Register to the system
     */
    public function register() {

        // validate fields
        $this->validator->mapFieldsRules([
            'username' => ['required', ['lengthMin', 5], 'alphaNum'],
            'password' => ['required', ['lengthMin', 5]]
        ]);
        $this->validator = $this->validator->withData($this->body);

        if (!$this->validator->validate()) {
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Validation failed',
                'data'      => $this->validator->errors()
            ], 200);
        }

        // if passed create JWT
        if ($this->userService->register($this->body['username'], $this->body['password'])) {
            $jwt = $this->jwtAuth->create($this->body['username']);

            return new JsonResponse([
                'status'    => 'success',
                'msg'       => 'Success',
                'data'      => [
                    'token' => $jwt,
                    'redirect' => '/customers.html'
                ]
            ], 200);
        }
        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'Registration failed',
            'data'      => []
        ], 200);
    }
}
