<?php
require_once '../vendor/autoload.php';

use App\Core\Router;

session_start();

// Создаем и настраиваем роутер
$router = new Router();

// Маршруты аутентификации
$router->add('login', 'AuthController', 'login');
$router->add('register', 'AuthController', 'register');
$router->add('logout', 'AuthController', 'logout');

// Маршруты задач
$router->add('tasks', 'TaskController', 'index');
$router->add('tasks/create', 'TaskController', 'create');
$router->add('tasks/edit', 'TaskController', 'edit');
$router->add('tasks/delete', 'TaskController', 'delete');
$router->add('tasks/update-status', 'TaskController', 'updateStatus');

// Запускаем маршрутизацию
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
?>