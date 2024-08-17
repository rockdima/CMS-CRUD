<?php

namespace App\Middlewares;

use Exception;
use Laminas\Diactoros\ServerRequest;

class IOSanitize {

    public function handle( $request, $next) {

        $sanitizedBody = array_map([$this, 'sanitize'], $request->getParsedBody());
        $newRequest = $request->withParsedBody($sanitizedBody);

        return $next($newRequest);
    }

    public function sanitize($data) {
        if(is_array($data))
            return array_map([$this, 'sanitize'], $data);
    
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}
