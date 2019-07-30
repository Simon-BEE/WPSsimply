<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;

class AdminController extends Controller
{
    /**
     * Vérifie les droits d'accès
     * Récupère les tables supplier, warehouse, product et user
     */
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('product');
        $this->loadModel('user');
    }

    /**
     * Affiche la page index de l'admin avec les derniers enregistrements dans la bdd
     *
     * @return string
     */
    public function index():string
    {

        $lastSupplier = (empty($this->supplier->isNotEmpty())) ? "AUCUN" : $this->supplier->findLast();
        $lastWarehouse = (empty($this->warehouse->isNotEmpty())) ? "AUCUN" : $this->warehouse->findLast();
        $lastProduct = (empty($this->product->isNotEmpty())) ? "AUCUN" : $this->product->findLast();
        $lastUser = (empty($this->user->isNotEmpty())) ? "AUCUN" : $this->user->findLast();
        
        return $this->render('admin/index.html', [
            'lastSupplier' => $lastSupplier,
            'lastWarehouse' => $lastWarehouse,
            'lastProduct' => $lastProduct,
            'lastUser' => $lastUser
        ]);
    }
}
