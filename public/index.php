<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';


// CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\UsersController;

$router = new Router();

$router->get('/auth/me', AuthController::class . "@getUser");
$router->post('/auth/register', AuthController::class . "@createUser");
$router->post('/auth/login', AuthController::class . "@loginUser");
$router->post('/auth/logout', AuthController::class . "@logout");

$router->get('/users', UsersController::class . "@getUsers");
$router->patch('/users/{id}', UsersController::class . "@updateUser");
$router->delete('/users/{id}', UsersController::class . "@deleteUser");


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['_method'])) {
    $_SERVER['REQUEST_METHOD'] = strtoupper($_POST['_method']);
}

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
