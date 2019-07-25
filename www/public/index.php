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
    ->match('/profile', 'user#profile', 'profile')
    ->get('/logout', 'user#logout', 'logout')

    //Fournisseurs
    ->match('/supplier/add', 'supplier#add', 'supplier_add')
    ->get('/suppliers', 'supplier#index', 'suppliers_all')
    ->get('/supplier/[*:slug]-[i:id]', 'supplier#show', 'supplier_show')
    ->match('/supplier/[*:slug]-[i:id]/edit', 'supplier#edit', 'supplier_edit')

    //Entrepôts
    ->match('/warehouse/add', 'warehouse#add', 'warehouse_add')
    ->get('/warehouses', 'warehouse#index', 'warehouses_all')
    ->get('/warehouse/[*:slug]-[i:id]', 'warehouse#show', 'warehouse_show')
    ->match('/warehouse/[*:slug]-[i:id]/edit', 'warehouse#edit', 'warehouse_edit')

    //Produits
    ->match('/product/add', 'product#add', 'product_add')
    ->get('/products', 'product#index', 'products_all')
    ->match('/product/[*:slug]-[i:id]', 'product#show', 'product_show')
    ->match('/product/[*:slug]-[i:id]/edit', 'product#edit', 'product_edit')
    
    ->run();
