<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->loadModel('user');
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('product');
        $this->loadModel('productWarehouse');
    }

    public function index()
    {
        $this->onlyAdmin();
        
        $all = $this->user->all();
        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->user,
                $this->generateUrl('admin_user_all')
            );
            $users = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }
        
        return $this->render('admin/user/index.html', [
            'title' => 'Affiche tous les utilisateurs', 
            'users' => $users, 
            'pagination' => $pagination]);
    }

    public function show($id)
    {
        $this->onlyAdmin();
        $user = $this->user->find($id);
        $role = $user->getRole();

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('mail', ['require'])
            ->field('role', ['require']);
        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();
            if (empty($errors)) {

                if ($role != $datas['role']) {

                    if ($role == 1) {
                        $supplier = $this->supplier->find($id, 'user_id');
                        if ($supplier) {
                            $idSupplier = $supplier->getId();
                            $productsBySupplier = $this->product->findAll($idSupplier, 'supplier_id');

                            foreach ($productsBySupplier as $value) {
                                $productIds[] = $value->getId();
                            }

                            foreach ($productIds as $value) {
                                $this->productWarehouse->delete($value, 'product_id');
                            }

                            $this->supplier->delete($idSupplier);
                            $this->product->delete($idSupplier, 'supplier_id');
                        }

                    }elseif($role == 2){
                        $warehouse = $this->warehouse->find($id, 'user_id');
                        if ($warehouse) {
                            $idWarehouse = $warehouse->getId();
                            $this->warehouse->delete($idWarehouse);
                            $this->productWarehouse->delete($idWarehouse, 'warehouse_id');
                        }

                    }else{
                        die('c\'est raté');
                    }
                }
                $this->user->update($id, 'id', $datas);
                $this->flash()->addSuccess('Les données de l\'utilisateur ont bien été modifiées');
            }
        }

        return $this->render('admin/user/show.html', [
            'title' => 'Modifier un utilisateur', 
            'user' => $user]);
    }
}