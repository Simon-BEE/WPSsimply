<?php
namespace App\Controller;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->loadModel('product');
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
        $this->loadModel('productWarehouse');
    }

    public function add()
    {
        if ($_SESSION['auth']->getRole() != 1) {
            header('location: /');
        }

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('toxicity', ['require'])
            ->field('volume', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();

            if (empty($errors)) {

                if ($datas['toxicity'] == 1) {
                    $datas['toxicity'] = '0';
                }else{
                    $datas['toxicity'] = '1';
                }

                $supplier_id = $this->supplier->find($_SESSION['auth']->getId(), 'user_id')->getId();
                $datas['supplier_id'] = $supplier_id;

                $this->product->create($datas);
                $this->flash()->addSuccess('Votre produit a bien été ajouté!');

                $slugify = new Slugify();
                $product = $this->product->find($this->product->last());
                $slug = $slugify->slugify($product->getName());
                $url = $this->generateUrl('product_show', ['slug' => $slug, 'id' => $product->getId()]);
                
                header('location: '.$url);
                exit();
            }
        }

        return $this->render('product/add.html');
    }

    public function edit($slug, $id)
    {
        $product = $this->product->find($id);
        if (!$product) {
            header('location: /products');
            exit();
        }
        
        if ($_SESSION['auth']->getRole() != 1) {
            header('location: /');
        }

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('toxicity', ['require'])
            ->field('volume', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                if ($datas['toxicity'] == 1) {
                    $datas['toxicity'] = '0';
                }else{
                    $datas['toxicity'] = '1';
                }

                $this->product->update($id, 'id', $datas);
                $this->flash()->addSuccess('Votre produit a bien été modifié!');
                
                $url = $this->generateUrl('product_show', ['slug' => $slug, 'id' => $id]);
                header('location: '.$url);
                exit();
            }
        }

        return $this->render('product/edit.html', ['product' => $product]);
    }

    public function show($slug, $id)
    {
        $product = $this->product->find($id);
        if (!$product) {
            header('location: /products');
            exit();
        }

        $supplier = $this->supplier->find($product->getSupplierId(), 'id');
        if ($_SESSION['auth']) {
            if ($supplier->getUserId() === $_SESSION['auth']->getId()) {
                $mine = true;
            }

            $warehouse = $this->warehouse->find($_SESSION['auth']->getId(), 'user_id');
            if ($warehouse) {
            $already = $this->productWarehouse->existing($id, $warehouse->getId());
                if (!empty($_POST) && !$already) {
                    $fields['product_id'] = $id;
                    $fields['warehouse_id'] = $warehouse->getId();
                    
                    $this->productWarehouse->create($fields);
                }

                if ($already) {
                    $addProduct = 'already';
                }else{
                    $addProduct = 'ok';
                }
            }
        }

        $slugify = new Slugify();
        $slugSupplier = $slugify->slugify($supplier->getSocial());
        $idSupplier = $supplier->getId();
        $urlSupplier = $this->generateUrl('supplier_show', ['slug' => $slugSupplier, 'id' => $idSupplier]);
        
        return $this->render('product/show.html', [
            'product' => $product, 
            'mine' => $mine, 
            'supplier' => $supplier,
            'addProduct' => $addProduct,
            'urlSupplier' => $urlSupplier]);
    }

    public function index()
    {
        $all = $this->product->all();

        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->product,
                $this->generateUrl('products_all')
            );
            $products = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }

        return $this->render('/product/index.html', ['products' => $products, 'pagination' => $pagination]);
    }

    public function delete()
    {
        if ($this->product->delete($_POST['id'])) {
            $this->productWarehouse->delete($_POST['id'], 'warehouse_id');
            echo 'ok';
        }else{
            echo 'error';
        }
    }
}