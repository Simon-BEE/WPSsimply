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

                $datas['supplier_id'] = $_SESSION['auth']->getId();
                $this->product->create($datas);
                $this->flash()->addSuccess('Votre société a bien été renseigné!');
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

    public function show($slug, $id)
    {
        $product = $this->product->find($id);
        $supplier = $this->supplier->find($product->getSupplierId(), 'user_id');
        if ($_SESSION['auth']) {
            if ($product->getSupplierId() === $_SESSION['auth']->getId()) {
                $mine = true;
            }
        }
        return $this->render('product/show.html', ['product' => $product, 'mine' => $mine, 'supplier' => $supplier]);
    }

    public function index()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->product,
            $this->generateUrl('products_all')
        );
        $products = $paginatedQuery->getItems();
        $pagination = $paginatedQuery->getNavHtml();

        return $this->render('/product/index.html', ['products' => $products, 'pagination' => $pagination]);
    }
}