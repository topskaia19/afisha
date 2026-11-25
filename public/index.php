<?php
/** Единая точка входа: сессия, маршруты. Запуск: php -S localhost:8000 -t public public/index.php */
require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\{AdminController, AuthController, EventController, HomeController};
use App\Core\Router;

// Статические файлы встроенного сервера отдаёт сам PHP
if (PHP_SAPI === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();

$r = new Router();
$r->add('GET', '/', [HomeController::class, 'index']);
$r->add('GET', '/events', [EventController::class, 'index']);
$r->add('GET', '/events/create', [EventController::class, 'createForm']);
$r->add('POST', '/events/create', [EventController::class, 'store']);
$r->add('GET', '/events/{id}', [EventController::class, 'show']);

$r->add('GET', '/register', [AuthController::class, 'registerForm']);
$r->add('POST', '/register', [AuthController::class, 'register']);
$r->add('GET', '/login', [AuthController::class, 'loginForm']);
$r->add('POST', '/login', [AuthController::class, 'login']);
$r->add('POST', '/logout', [AuthController::class, 'logout']);
$r->add('GET', '/forgot', [AuthController::class, 'forgotForm']);
$r->add('POST', '/forgot', [AuthController::class, 'forgot']);
$r->add('GET', '/reset/{token}', [AuthController::class, 'resetForm']);
$r->add('POST', '/reset/{token}', [AuthController::class, 'reset']);

$r->add('GET', '/admin', [AdminController::class, 'dashboard']);
$r->add('GET', '/admin/users', [AdminController::class, 'users']);
$r->add('POST', '/admin/users/{id}/role', [AdminController::class, 'setRole']);
$r->add('POST', '/admin/users/{id}/delete', [AdminController::class, 'deleteUser']);
$r->add('GET', '/admin/admins/new', [AdminController::class, 'adminForm']);
$r->add('POST', '/admin/admins/new', [AdminController::class, 'createAdmin']);
$r->add('GET', '/admin/events', [AdminController::class, 'events']);
$r->add('GET', '/admin/events/{id}/edit', [AdminController::class, 'editEvent']);
$r->add('POST', '/admin/events/{id}/edit', [AdminController::class, 'updateEvent']);
$r->add('POST', '/admin/events/{id}/approve', [AdminController::class, 'approveEvent']);
$r->add('POST', '/admin/events/{id}/delete', [AdminController::class, 'deleteEvent']);
$r->add('GET', '/admin/categories', [AdminController::class, 'categories']);
$r->add('POST', '/admin/categories', [AdminController::class, 'addCategory']);
$r->add('POST', '/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory']);

$r->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
