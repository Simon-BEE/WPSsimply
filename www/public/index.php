<?php
$basePath = dirname(__dir__) . DIRECTORY_SEPARATOR;

require_once $basePath . 'vendor/autoload.php';

$app = App\App::getInstance();
$app->setStartTime();
$app::load();

$app->getRouter($basePath)
    ->get('/', 'Site#index', 'home')
    //Utilisateurs
    ->match('/login', 'user#login', 'login')
    ->match('/register', 'user#register', 'register')
    ->run();
