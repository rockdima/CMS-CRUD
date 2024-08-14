<?php

namespace App\Middlewares;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Laminas\Diactoros\Response\JsonResponse;
use Exception;

class JWTAuth {

    /**
     * Checks if JWT correct
     */
    public function handle($request, $next) {

        // get from ENV
        $key = $_ENV['JWK_KEY'];

        // get jwt from header
        try {
            $JWToken = @explode(' ', $request->getHeader('Authorization')[0])[1];
            if (is_null($JWToken)) {
                throw new Exception('No Authorization header!');
            }
        } catch (Exception $e) {
            return new JsonResponse([
                'status'    => 'authError',
                'msg'       => 'Invalid token: ' . $e->getMessage(),
                'data'      => []
            ], 401);
        }

        try {
            $decoded = JWT::decode($JWToken, new Key($key, 'HS256'));
        } catch (Exception $e) {
            return new JsonResponse([
                'status'    => 'authError',
                'msg'       => 'Invalid token: ' . $e->getMessage(),
                'data'      => []
            ], 401);
        }

        return $next($request);
    }

     /**
     * Create new JWT
     * 
     * @param string $username username
     */
    public function create(string $username): string {

        // get from ENV
        $key = $_ENV['JWK_KEY'];

        $issuedAt = time();
        $expiration = $issuedAt + 3600; // Token valid for 1 hour

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expiration,
            'data' => [
                'userName' => $username,
            ]
        ];

        return JWT::encode($payload, $key, 'HS256');
    }
}
