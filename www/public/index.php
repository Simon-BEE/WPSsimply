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
    ->get('/profile', 'user#profile', 'profile')
    ->get('/logout', 'user#logout', 'logout')

    //Fournisseurs
    ->match('/supplier/add', 'supplier#add', 'supplier_add')
    ->get('/suppliers', 'supplier#index', 'suppliers_all')
    ->get('/supplier/[*:slug]-[i:id]', 'supplier#show', 'supplier_show')
    ->match('/supplier/[*:slug]-[i:id]/edit', 'supplier#edit', 'supplier_edit')
    ->run();
