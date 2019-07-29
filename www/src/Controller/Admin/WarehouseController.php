<?php
namespace App\Controller\Admin;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;
use App\Controller\PaginatedQueryAppController;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->loadModel('warehouse');
        $this->loadModel('city');
        $this->loadModel('user');
    }

    public function index()
    {
        $this->onlyAdmin();

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

    public function show($slug, $id)
    {
        $this->onlyAdmin();

        $warehouse = $this->warehouse->find($id);

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
                
                header('location: '. $this->generateUrl('admin_warehouse_show', [
                    'slug' => $slugNew, 
                    'id' => $id]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $cities = $this->city->all();
        return $this->render('admin/warehouse/show.html', [
            'title' => 'Modifier les données de cet entrepôt',
            'warehouse' => $warehouse,
            'cities' => $cities
        ]);
    }

    public function add()
    {
        $this->onlyAdmin();

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
                
                header('location: '. $this->generateUrl('admin_warehouse_show', [
                    'slug' => $slugNew, 
                    'id' => $this->warehouse->last()]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir tous les champs');
            }
        }

        $userCanBeWarehouseBoss = $this->user->findAll(2, 'role');
        foreach ($userCanBeWarehouseBoss as $value) {
            $userWarehouse[$value->getId()] = $value;
        }

        foreach ($userWarehouse as $key => $value) {
            if (!$this->warehouse->find($key, 'user_id')) {
                    $becomeWarehouse[] = $value;
            }
            
        }
        // dump($userCanBeWarehouseBoss);
        // dd($userWarehouse);
        $lastId = $this->warehouse->last();
        $cities = $this->city->all();

        return $this->render('admin/warehouse/add.html', [
            'title' => 'Ajouter un nouvel entrepôt',
            'cities' => $cities,
            'userCanBeWarehouseBoss' => $becomeWarehouse,
            'lastId' => $lastId
        ]);
    }
}