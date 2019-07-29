<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('user');
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('product');
        $this->loadModel('productWarehouse');
    }

    public function index()
    {
        
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
        $user = $this->user->find($id);
        if (!$user) {
            header('location: /admin/user');
            exit();
        }
        $role = $user->getRole();

        $form = new FormController();
        if ($role == 7) {
            $form->field('name', ['require'])
                ->field('mail', ['require']);
        }else{
            $form->field('name', ['require'])
                ->field('mail', ['require'])
                ->field('role', ['require']);
        }

        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();
            if (empty($errors)) {

                if ($user->getMail() != $datas["mail"]) {
                    if ($this->user->find($datas["mail"], "mail")) {
                        throw new \Exception("L'email est déjà utilisé par un autre utilisateur");
                    }
                }

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

                    }
                }
                $this->user->update($id, 'id', $datas);
                $this->flash()->addSuccess('Les données de l\'utilisateur ont bien été modifiées');
                $user = $this->user->find($id);
            }
        }

        return $this->render('admin/user/show.html', [
            'title' => 'Modifier un utilisateur', 
            'user' => $user]);
    }

    public function add()
    {

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('mail', ['require'])
            ->field('password', ['require', "length" => 6])
            ->field('role', ['require']);

        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas  = $form->getDatas();
            if (empty($errors)) {
                $user = $this->user;
                if ($user->find($datas["mail"], "mail")) {
                    throw new \Exception("L'email de l'utilisateur existe déjà");
                }
                $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);
                $this->user->create($datas);
                $this->flash()->addSuccess('L\'utilisateur est bien enregistré');
                $url = $this->generateUrl('admin_user_show', ['id' => $this->user->last()]);
                header('location: '.$url);
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir les champs !');
            }
        }

        $lastUserId = $this->user->last();
        return $this->render('admin/user/add.html', [
            'title' => 'Ajouter un nouvel utilisateur',
            'lastUserId' => $lastUserId
        ]);
    }

    public function delete()
    {
        $user = $this->user->find($_POST['id']);
        if ($this->user->delete($_POST['id'], 'id', $user)) {
            echo 'ok';
        }else{
            echo 'error';
        }
    }
}