<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('product');
        $this->loadModel('user');
    }

    public function index()
    {
        if (empty($_SESSION['auth']) || $_SESSION['auth']->getRole() != 7) {
            header('location: /');
            exit();
        }

        $lastSupplier = $this->supplier->findLast();
        $lastWarehouse = $this->warehouse->findLast();
        $lastProduct = $this->product->findLast();
        $lastUser = $this->user->findLast();
        
        return $this->render('admin/index.html',[
            'lastSupplier' => $lastSupplier,
            'lastWarehouse' => $lastWarehouse,
            'lastProduct' => $lastProduct,
            'lastUser' => $lastUser
        ]);
    }
}