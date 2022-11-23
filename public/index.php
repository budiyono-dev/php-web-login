<?php

require_once __DIR__ . '/../vendor/autoload.php';

use app\App\Router;
use app\Config\Database;
use app\Controller\HomeController;
use app\Controller\UserController;
use app\Middleware\MustLoginMiddleware;
use app\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

Router::add('GET', '/', HomeController::class, 'index'); 

Router::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]); 
Router::add('POST', '/users/register', UserController::class, 'postregister', [MustNotLoginMiddleware::class]); 
Router::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]); 
Router::add('POST', '/users/login', UserController::class, 'postlogin', [MustNotLoginMiddleware::class]); 
Router::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]); 
Router::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]); 
Router::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]); 



Router::run();