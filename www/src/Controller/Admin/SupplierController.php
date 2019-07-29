<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('supplier');
        $this->loadModel('user');
        $this->loadModel('product');
    }

    public function index()
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

    public function show($slug, $id)
    {

        $supplier = $this->supplier->find($id);
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
                
                header('location: '. $this->generateUrl('admin_supplier_show', ['slug' => $slugNew, 'id' => $id]));
                exit();
            }
        }

        return $this->render('admin/supplier/show.html', [
            'title' => 'Modifier un fournisseur', 
            'supplier' => $supplier,
            'products' => $products
            ]);
    }

    public function add()
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
                
                header('location: '. $this->generateUrl('admin_supplier_show', [
                    'slug' => $slugNew, 
                    'id' => $this->supplier->last()]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $userCanBeSupplier = $this->user->findAll(1, 'role');
        foreach ($userCanBeSupplier as $value) {
            $userSupplier[$value->getId()] = $value;
        }

        foreach ($userSupplier as $key => $value) {
            if (!$this->supplier->find($key, 'user_id')) {
                    $becomeSupplier[] = $value;
            }
            
        }
        $lastId = $this->supplier->last();
        return $this->render('admin/supplier/add.html', [
            'title' => 'Ajouter un fournisseur',
            'lastId' => $lastId, 
            'userCanBeSupplier' => $becomeSupplier]);
    }
}