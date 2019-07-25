<?php
namespace App\Controller;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->loadModel('warehouse');
        $this->loadModel('city');
        $this->loadModel('productWarehouse');
        $this->loadModel('product');
    }

    public function index()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->warehouse,
            $this->generateUrl('warehouses_all')
        );
        $warehouses = $paginatedQuery->getItems();
        $pagination = $paginatedQuery->getNavHtml();
        return $this->render('warehouse/index.html', ['warehouses' => $warehouses, 'pagination' => $pagination]);
    }

    public function add()
    {
        if ($this->warehouse->find($_SESSION['auth']->getId(), 'user_id') &&
            ($_SESSION['auth']->getRole() != 2)) {
            header('location: /');
        }

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('city_id', ['require'])
            ->field('surface', ['require'])
            ->field('address', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                $datas['user_id'] = $_SESSION['auth']->getId();
                $this->warehouse->create($datas);
                $this->flash()->addSuccess('Votre société a bien été renseigné!');
                $slugify = new Slugify();
                $warehouse = $this->warehouse->find($this->warehouse->last());
                $slug = $slugify->slugify($warehouse->getName());
                $url = $this->generateUrl('warehouse_show', ['slug' => $slug, 'id' => $warehouse->getId()]);
                header('location: '.$url);
                exit();
            }
        }

        $cities = $this->city->all();
        return $this->render('warehouse/add.html', ['cities' => $cities]);
    }

    public function show($slug, $id)
    {
        $warehouse = $this->warehouse->find($id);
        if ($_SESSION['auth']) {
            if ($warehouse->getUserId() === $_SESSION['auth']->getId()) {
                $mine = true;
            }
        }
        $productsArray = $this->productWarehouse->findAll($id, 'warehouse_id');
        foreach ($productsArray as $line) {
            $productsId[] = $line->getProductId();
        }
        foreach ($productsId as $value) {
            $products[] = $this->product->find($value);
        }
        
        $city = $this->city->find($warehouse->getCityId())->getName();
        return $this->render('warehouse/show.html', ['warehouse' => $warehouse, 
        'city' => $city, 
        'mine' => $mine,
        'products' => $products]);
    }

    public function edit($slug, $id)
    {
        $warehouse = $this->warehouse->find($id);
        if ($_SESSION['auth']->getId() !== $warehouse->getUserId()) {
            die('la');
            header('location: '. $this->generateUrl('warehouse_show', ['slug' => $slug, 'id' => $id]));
        }

        $form = new FormController();
        $form->field('name', ['require'])
            ->field('city_id', ['require'])
            ->field('surface', ['require'])
            ->field('address', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                
                $this->warehouse->update($id, 'id', $datas);
                $this->flash()->addSuccess('Votre société a bien été modifié!');
                
                header('location: '. $this->generateUrl('warehouse_show', ['slug' => $slug, 'id' => $id]));
                exit();
            }
        }
        $cities = $this->city->all();
        return $this->render('warehouse/edit.html', ['warehouse' => $warehouse, 'cities' => $cities]);
    }
}