<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('product');
        $this->loadModel('supplier');
        $this->loadModel('productWarehouse');
    }

    public function index()
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

    public function show($slug, $id)
    {
        $product = $this->product->find($id);
        if (!$product) {
            header('location: /admin/product');
            exit();
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
                }else{
                    $datas['toxicity'] = '1';
                }

                $this->product->update($id, 'id', $datas);
                $this->flash()->addSuccess('Le produit a bien été modifié');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                header('location: '. $this->generateUrl('admin_product_show', [
                    'slug' => $slugNew, 
                    'id' => $this->product->last()]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        return $this->render('admin/product/show.html', [
            'title' => 'Modifier un produit',
            'product' => $product
        ]);
    }

    public function add()
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
                }else{
                    $datas['toxicity'] = '1';
                }

                $this->product->create($datas);
                $this->flash()->addSuccess('Un nouveau produit a bien été créé');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                header('location: '. $this->generateUrl('admin_product_show', [
                    'slug' => $slugNew, 
                    'id' => $this->product->last()]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        return $this->render('admin/product/add.html',[
            'title' => 'Ajouter un nouveau produit',
            'suppliers' => $suppliers
        ]);
    }

    public function delete()
    {
        if ($this->product->delete($_POST['id'])) {
            echo 'ok';
        }else{
            echo 'error';
        }
    }
}