<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Model\Entity\WarehouseEntity;
use App\Controller\PaginatedQueryAppController;

class WarehouseController extends Controller
{
    /**
     * Vérifie les droits d'accès
     * Récupère les tables city, product, warehouse, product_warehouse et user
     */
    public function __construct()
    {
        $this->onlyAdmin();
        $this->loadModel('warehouse');
        $this->loadModel('city');
        $this->loadModel('user');
        $this->loadModel('product');
        $this->loadModel('productWarehouse');
    }

    /**
     * Affichage de la vu de tous les entrepôts en admin
     *
     * @return string
     */
    public function index():string
    {
        $all = $this->warehouse->all();
        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->warehouse,
                $this->generateUrl('admin_warehouse_all')
            );
            $warehouses = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }

        $cities = $this->city->all();

        
        return $this->render('admin/warehouse/index.html', [
            'title' => 'Affiche tous les entrepôts',
            'warehouses' => $warehouses,
            'pagination' => $pagination,
            'cities' => $cities
        ]);
    }

    /**
     * Affichage de la vu de modification d'un entrepôt
     * et du traitement du formulaire de modification
     *
     * @return string
     */
    public function show($slug, $id):string
    {
        $warehouse = $this->warehouse->find($id);
        if (!$warehouse) {
            $this->redirect('/admin/warehouse');
        }

        $myProducts = $this->productsInWarehouse($warehouse);
        
        $form = new FormController();
        $form->field('name', ['require'])
            ->field('surface', ['require'])
            ->field('address', ['require'])
            ->field('city_id', ['require']);

        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                $this->warehouse->update($id, 'id', $datas);
                $this->flash()->addSuccess('L\'entrepôt a bien été modifié');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                $this->redirect($this->generateUrl('admin_warehouse_show', [
                    'slug' => $slugNew,
                    'id' => $id]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $cities = $this->city->all();
        return $this->render('admin/warehouse/show.html', [
            'title' => 'Modifier les données de cet entrepôt',
            'warehouse' => $warehouse,
            'cities' => $cities,
            'products' => $myProducts
        ]);
    }

    /**
     * Affichage de la vu d'ajout d'un entrepôt
     * et du traitement du formulaire d'ajout
     *
     * @return string
     */
    public function add():string
    {

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('surface', ['require'])
            ->field('address', ['require'])
            ->field('city_id', ['require'])
            ->field('user_id', ['require']);

        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                $this->warehouse->create($datas);
                $this->flash()->addSuccess('Un nouveau gérant d\'entrepôt a bien été créé');

                $slugify = new Slugify();
                $slugNew = $slugify->slugify($datas['name']);
                
                $this->redirect($this->generateUrl('admin_warehouse_show', [
                    'slug' => $slugNew,
                    'id' => $this->warehouse->last()]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $userCanBeWarehouseBoss = $this->user->findAll(2, 'role');
        foreach ($userCanBeWarehouseBoss as $value) {
            $userWarehouse[$value->getId()] = $value;
        }

        if ($userWarehouse) {
            foreach ($userWarehouse as $key => $value) {
                if (!$this->warehouse->find($key, 'user_id')) {
                        $becomeWarehouse[] = $value;
                }
            }
        }
        
        $lastId = $this->warehouse->last();
        $cities = $this->city->all();

        return $this->render('admin/warehouse/add.html', [
            'title' => 'Ajouter un nouvel entrepôt',
            'cities' => $cities,
            'userCanBeWarehouseBoss' => $becomeWarehouse,
            'lastId' => $lastId
        ]);
    }

    /**
     * Affichage de la vu pour ajouter un produit a un entrepot
     * et du traitement du formulaire d'ajout
     *
     * @return string
     */
    public function addProduct($slug, $id):string
    {
        $warehouse = $this->warehouse->find($id);
        
        $products = $this->productsNotInWarehouse($id);
        
        $form = new FormController();

        foreach ($products as $value) {
            $form->field('product_id'.$value->getId());
        }

        $errors = $form->hasErrors();

        if (!isset($errors['post'])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $fields['warehouse_id'] = $id;
                foreach ($datas as $value) {
                    if (!empty($value)) {
                        $fields['product_id'] = $value;
                        $this->productWarehouse->create($fields);
                    }
                }
                $this->flash()->addSuccess('Le ou les produits ont bien été ajoutés à cet entrepôt');
                
                $this->redirect($this->generateUrl('admin_warehouse_show', [
                    'slug' => $slug,
                    'id' => $id]));
            } else {
                $this->flash()->addAlert('Veillez à choisir au moins un produit');
            }
        }

        return $this->render('admin/warehouse/product.html', [
            'title' => 'Ajouter des produits à un entrepôt',
            'products' => $products,
            'warehouse' => $warehouse
        ]);
    }

    /**
     * Gere la suppression d'un entrepot
     *
     * @return void
     */
    public function delete():void
    {
        if ($this->warehouse->delete($_POST['id'])) {
            echo 'ok';
        } else {
            echo 'error';
        }
    }

    /**
     * Retourne tous les produits d'un entrepôt
     *
     * @return array
     */
    private function productsInWarehouse(WarehouseEntity $warehouse):?array
    {
        $productsId = $this->productWarehouse->findAll($warehouse->getId(), 'warehouse_id');
        foreach ($productsId as $id) {
            $products[] = $this->product->find($id->getProductId());
        }
        return $products;
    }

    /**
     * Retourne tous les produits qu'un entrepot peut ajouter
     *
     * @return array
     */
    private function productsNotInWarehouse($id):array
    {
        $allProductsSql = $this->product->all();
        $productsInWarehouse = $this->productWarehouse->findAll($id, 'warehouse_id');
        $products = [];

        if ($allProductsSql && $productsInWarehouse) {
            foreach ($allProductsSql as $oneProductSql) {
                $allProducts[$oneProductSql->getId()] = $oneProductSql;
            }
    
            foreach ($productsInWarehouse as $productInWarehouse) {
                $allPW[$productInWarehouse->getProductId()] = $productInWarehouse;
            }

            foreach ($allProducts as $keyAll => $product) {
                foreach ($allPW as $keyPW => $onePW) {
                    if (!array_key_exists($keyAll, $allPW)) {
                        $productsDuplicate[] = $product;
                    }
                }
            }

            if ($productsDuplicate) {
                $products = array_unique($productsDuplicate, SORT_REGULAR);
            }
        } elseif (!$productsInWarehouse) {
            $products = $allProductsSql;
        }
        
        return $products;
    }
}
