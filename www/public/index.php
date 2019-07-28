<?php
$basePath = dirname(__dir__) . DIRECTORY_SEPARATOR;

require_once $basePath . 'vendor/autoload.php';

$app = App\App::getInstance();
$app->setStartTime();
$app::load();

$app->getRouter($basePath)
    //Site
    ->get('/', 'Site#index', 'home')
    ->get('/legal-notices', 'site#notices', 'legal_notices')
    ->get('/404', 'site#notfound', 'not_found')

    //Administration
    ->get('/admin', 'admin\Admin#index', 'admin')

    ->get('/admin/supplier', 'admin\Supplier#index', 'admin_supplier_all')
    ->match('/admin/supplier/add', 'admin\Supplier#add', 'admin_supplier_add')
    ->match('/admin/supplier/[*:slug]-[i:id]', 'admin\Supplier#show', 'admin_supplier_show')

    ->get('/admin/warehouse', 'admin\Warehouse#index', 'admin_warehouse_all')
    ->get('/admin/product', 'admin\Product#index', 'admin_product_all')

    ->get('/admin/user', 'admin\User#index', 'admin_user_all')
    ->match('/admin/user/add', 'admin\User#add', 'admin_user_add')
    ->match('/admin/user/[*:id]', 'admin\User#show', 'admin_user_show')

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
