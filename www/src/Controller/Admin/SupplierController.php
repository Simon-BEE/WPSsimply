<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class SupplierController extends Controller
{
    /**
     * Vérifie les droits d'accès
     * Récupère les tables supplier, product et user
     */
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('supplier');
        $this->loadModel('user');
        $this->loadModel('product');
    }

    /**
     * Affichage de la vu de tous les fournisseurs en admin
     *
     * @return string
     */
    public function index():string
    {
        
        $all = $this->supplier->all();
        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->supplier,
                $this->generateUrl('admin_supplier_all')
            );
            $suppliers = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }
        
        return $this->render('admin/supplier/index.html', [
            'title' => 'Affiche tous les fournisseurs',
            'suppliers' => $suppliers,
            'pagination' => $pagination]);
    }

    /**
     * Affichage de la vu pour modifier un fournisseur
     * Et traitement de son formulaire
     *
     * @return string
     */
    public function show(string $slug, int $id): string
    {

        $supplier = $this->supplier->find($id);
        if (!$supplier) {
            $this->redirect('/admin/supplier');
        }
        
        $products = $this->product->findAll($id, 'supplier_id');

        $form = new FormController();
        $form->field('social', ['require'])
            ->field('address', ['require']);
        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $this->supplier->update($id, 'id', $datas);
                $this->flash()->addSuccess('Votre société a bien été modifié!');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['social']);
                
                $this->redirect($this->generateUrl('admin_supplier_show', ['slug' => $slugNew, 'id' => $id]));
            }
        }

        return $this->render('admin/supplier/show.html', [
            'title' => 'Modifier un fournisseur',
            'supplier' => $supplier,
            'products' => $products
            ]);
    }

    /**
     * Affichage de la vu pour ajouter un fournisseur
     * Et traitement de son formulaire
     *
     * @return string
     */
    public function add():string
    {

        $form = new FormController();
        $form->field('social', ['require'])
            ->field('address', ['require'])
            ->field('user_id', ['require']);
        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $this->supplier->create($datas);
                $this->flash()->addSuccess('Un nouveau fournisseur a été créé');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['social']);
                
                $this->redirect($this->generateUrl('admin_supplier_show', [
                    'slug' => $slugNew,
                    'id' => $this->supplier->last()]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $userCanBeSupplier = $this->user->findAll(1, 'role');
        foreach ($userCanBeSupplier as $value) {
            $userSupplier[$value->getId()] = $value;
        }

        if ($userSupplier) {
            foreach ($userSupplier as $key => $value) {
                if (!$this->supplier->find($key, 'user_id')) {
                        $becomeSupplier[] = $value;
                }
            }
        }
        $lastId = $this->supplier->last();
        return $this->render('admin/supplier/add.html', [
            'title' => 'Ajouter un fournisseur',
            'lastId' => $lastId,
            'userCanBeSupplier' => $becomeSupplier]);
    }

    /**
     * Gère la suppression d'un fournisseur
     *
     * @return void
     */
    public function delete():void
    {
        if ($this->supplier->delete($_POST['id'])) {
            echo 'ok';
        } else {
            echo 'error';
        }
    }
}
