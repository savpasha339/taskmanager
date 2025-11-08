<?php
require_once '../vendor/autoload.php';

use App\Core\Router;

session_start();

// Отладочная информация
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("GET parameters: " . print_r($_GET, true));

$router = new Router();

$router->add('login', 'AuthController', 'login');
$router->add('register', 'AuthController', 'register');
$router->add('logout', 'AuthController', 'logout');

$router->add('tasks', 'TaskController', 'index');
$router->add('tasks/history', 'TaskController', 'history');
$router->add('tasks/create', 'TaskController', 'create');
$router->add('tasks/edit', 'TaskController', 'edit');
$router->add('tasks/delete', 'TaskController', 'delete');
$router->add('tasks/update-status', 'TaskController', 'updateStatus');

$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
?>