<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class ProductController extends Controller
{
    /**
     * Vérifie les droits d'accès
     * Récupère les tables supplier, product et product_warehouse
     */
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('product');
        $this->loadModel('supplier');
        $this->loadModel('productWarehouse');
    }

    /**
     * Affichage de la vu de tous les produits en admin
     *
     * @return string
     */
    public function index():string
    {
        $all = $this->product->all();
        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->product,
                $this->generateUrl('admin_product_all')
            );
            $products = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }

        return $this->render('admin/product/index.html', [
            'title' => 'Tous les produits',
            'products' => $products,
            'pagination' => $pagination
        ]);
    }

    /**
     * Affichage de la vu de modification d'un produit
     * et du traitement du formulaire de modification
     *
     * @return string
     */
    public function show($slug, $id):string
    {
        $product = $this->product->find($id);
        if (!$product) {
            $this->redirect('/admin/product');
        }
        
        $form = new FormController();
        $form->field('name', ['require'])
            ->field('toxicity', ['require'])
            ->field('volume', ['require']);
        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                if ($datas['toxicity'] == 1) {
                    $datas['toxicity'] = '0';
                } else {
                    $datas['toxicity'] = '1';
                }

                $this->product->update($id, 'id', $datas);
                $this->flash()->addSuccess('Le produit a bien été modifié');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                $this->redirect($this->generateUrl('admin_product_show', [
                    'slug' => $slugNew,
                    'id' => $this->product->last()]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        return $this->render('admin/product/show.html', [
            'title' => 'Modifier un produit',
            'product' => $product
        ]);
    }

    /**
     * Affichage de la vu d'ajout d'un produit
     * et du traitement du formulaire d'ajout
     *
     * @return string
     */
    public function add():string
    {
        $suppliers = $this->supplier->all();

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('supplier_id', ['require'])
            ->field('toxicity', ['require'])
            ->field('volume', ['require']);
        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                if ($datas['toxicity'] == 1) {
                    $datas['toxicity'] = '0';
                } else {
                    $datas['toxicity'] = '1';
                }

                $this->product->create($datas);
                $this->flash()->addSuccess('Un nouveau produit a bien été créé');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                $this->redirect($this->generateUrl('admin_product_show', [
                    'slug' => $slugNew,
                    'id' => $this->product->last()]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        return $this->render('admin/product/add.html', [
            'title' => 'Ajouter un nouveau produit',
            'suppliers' => $suppliers
        ]);
    }

    /**
     * Gère la suppression d'un produit
     *
     * @return void
     */
    public function delete():void
    {
        if ($this->product->delete($_POST['id'])) {
            echo 'ok';
        } else {
            echo 'error';
        }
    }
}
