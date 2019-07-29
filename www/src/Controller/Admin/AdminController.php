<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('product');
        $this->loadModel('user');
    }

    public function index()
    {

        $lastSupplier = (empty($this->supplier->isNotEmpty())) ? "AUCUN" : $this->supplier->findLast();
        $lastWarehouse = (empty($this->warehouse->isNotEmpty())) ? "AUCUN" : $this->warehouse->findLast();
        $lastProduct = (empty($this->product->isNotEmpty())) ? "AUCUN" : $this->product->findLast(); 
        $lastUser = (empty($this->user->isNotEmpty())) ? "AUCUN" : $this->user->findLast();
        
        return $this->render('admin/index.html',[
            'lastSupplier' => $lastSupplier,
            'lastWarehouse' => $lastWarehouse,
            'lastProduct' => $lastProduct,
            'lastUser' => $lastUser
        ]);
    }
}