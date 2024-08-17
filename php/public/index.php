<?php
// set allowed headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

define('BASE_PATH', __DIR__ . "/../");

require BASE_PATH . 'vendor/autoload.php';

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteParser\Std as StdParser;
use FastRoute\DataGenerator\GroupCountBased as GroupCountBasedDataGenerator;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Response\JsonResponse;

use App\Controllers\CustomerController;
use App\Controllers\UserController;

use App\utils\db\DBInterface;
use App\utils\db\MySQL;

use App\Middlewares\JWTAuth;
use App\Middlewares\IOSanitize;

$containerBuilder = new ContainerBuilder();

// set for DI Container
$containerBuilder->addDefinitions([
    DBInterface::class => function () {
        return new MySQL(
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_DB'],
            $_ENV['MYSQL_USER'],
            $_ENV['MYSQL_PASSWORD']
        );
    },
    ServerRequest::class => function () {
        return ServerRequestFactory::fromGlobals();
    }
]);

$container = $containerBuilder->build();

$routeCollector = new RouteCollector(new StdParser(), new GroupCountBasedDataGenerator());

// set routes groups
$routeCollector->addGroup('/customers', function (RouteCollector $r) {
    $r->addRoute('GET', '', [CustomerController::class, 'getAll', [JWTAuth::class]]);
    $r->addRoute('GET', '/{id}', [CustomerController::class, 'read', [JWTAuth::class]]);
    $r->addRoute('POST', '/create', [CustomerController::class, 'create', [JWTAuth::class]]);
    $r->addRoute('DELETE', '/delete/{id}', [CustomerController::class, 'delete', [JWTAuth::class]]);
    $r->addRoute('POST', '/update/{id}', [CustomerController::class, 'update', [JWTAuth::class]]);
});

$routeCollector->addGroup('/users', function (RouteCollector $r) {
    $r->addRoute('POST', '/login', [UserController::class, 'login', []]);
    $r->addRoute('POST', '/register', [UserController::class, 'register', []]);
});

$dispatcher = new GroupCountBased($routeCollector->getData());

$request = $container->get(ServerRequest::class);
$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

// get middleware for route
$middlewares = $routeInfo[1][2] ?? [];
$middlewares[] = IOSanitize::class;

// Middleware execution
$next = function ($request) use ($container, $routeInfo) {

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Not Found',
                'data'      => []
            ], 404);
            break;

        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Method Not Allowed',
                'data'      => []
            ], 405);
            break;

        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            // set updated request
            $container->set(ServerRequest::class, $request);

            list($controller, $method) = $handler;
            $controllerInstance = $container->get($controller);

            return call_user_func_array([$controllerInstance, $method], $vars);
            break;
    }
};

// add middlewares to next proccess
foreach ($middlewares as $middleware) {
    $middlewareObject = $container->get($middleware);
    $next = function ($request) use ($middlewareObject, $next) {
        return $middlewareObject->handle($request, $next);
    };
}

// Start the request
$response = $next($request);

// escape output
$bodyArray = json_decode($response->getBody(), true);
$sanitizeOutput = $container->get(IOSanitize::class);
$bodyArray['data'] = array_map([$sanitizeOutput, 'sanitize'], $bodyArray['data']);


// add headers
foreach ($response->getHeaders() as $header => $value) {
    header("{$header}: {$value[0]}");
}
http_response_code($response->getStatusCode());
echo json_encode($bodyArray);
