<?php
namespace App\Controller;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->loadModel('supplier');
    }

    public function index()
    {
        $paginatedQuery = new PaginatedQueryAppController(
            $this->supplier,
            $this->generateUrl('suppliers_all')
        );
        $suppliers = $paginatedQuery->getItems();
        $pagination = $paginatedQuery->getNavHtml();
        
        return $this->render('supplier/index.html', ['suppliers' => $suppliers, 'pagination' => $pagination]);
    }

    public function show($slug, $id)
    {
        $supplier = $this->supplier->find($id);
        if ($_SESSION['auth']) {
            if ($supplier->getUserId() === $_SESSION['auth']->getId()) {
                $mine = true;
            }
        }
        return $this->render('supplier/show.html', ['supplier' => $supplier, 'mine' => $mine]);
    }

    public function add()
    {
        if ($this->supplier->find($_SESSION['auth']->getId(), 'user_id') &&
        ($_SESSION['auth']->getRole() != 1)) {
            header('location: /');
        }

        $form = new FormController();
        $form->field('social', ['require'])
            ->field('address', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $datas['user_id'] = $_SESSION['auth']->getId();
                $this->supplier->create($datas);
                $this->flash()->addSuccess('Votre société a bien été renseigné!');
                $slugify = new Slugify();
                $supplier = $this->supplier->find($this->supplier->last());
                $slug = $slugify->slugify($supplier->getSocial());
                $url = $this->generateUrl('supplier_show', ['slug' => $slug, 'id' => $supplier->getId()]);
                header('location: '.$url);
                exit();
            }
        }

        return $this->render('supplier/add.html');
    }

    public function edit($slug, $id)
    {
        $supplier = $this->supplier->find($id);
        if ($_SESSION['auth']->getId() !== $supplier->getUserId()) {
            die('la');
            header('location: '. $this->generateUrl('supplier_show', ['slug' => $slug, 'id' => $id]));
        }

        $form = new FormController();
        $form->field('social', ['require'])
            ->field('address', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                
                $this->supplier->update($id, 'id', $datas);
                $this->flash()->addSuccess('Votre société a bien été modifié!');
                
                header('location: '. $this->generateUrl('supplier_show', ['slug' => $slug, 'id' => $id]));
                exit();
            }
        }
        return $this->render('supplier/edit.html', ['supplier' => $supplier]);
    }
}