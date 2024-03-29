<?php
namespace App\Controller;

use Cocur\Slugify\Slugify;
use Core\Controller\Controller;
use Core\Controller\FormController;

class SupplierController extends Controller
{
    /**
     * Récupère les tables product et supplier
     */
    public function __construct()
    {
        $this->loadModel('supplier');
        $this->loadModel('product');
    }

    /**
     * Affichage de la vu de tous les fournisseurs
     *
     * @return string
     */
    public function index(): string
    {
        $all = $this->supplier->all();
        if ($all) {
            $paginatedQuery = new PaginatedQueryAppController(
                $this->supplier,
                $this->generateUrl('suppliers_all')
            );
            $suppliers = $paginatedQuery->getItems();
            $pagination = $paginatedQuery->getNavHtml();
        }
        
        return $this->render('supplier/index.html', ['suppliers' => $suppliers, 'pagination' => $pagination]);
    }

    /**
     * Affichage de la vu d'un fournisseur
     *
     * @return string
     */
    public function show(string $slug, int $id): string
    {
        $supplier = $this->supplier->find($id);
        if (!$supplier) {
            $this->redirect('/suppliers');
        }

        if ($_SESSION['auth']) {
            if ($supplier->getUserId() === $_SESSION['auth']->getId()) {
                $mine = true;
            }
        }
        $productsArray = $this->product->findAll($supplier->getId(), 'supplier_id');
        return $this->render('supplier/show.html', [
            'supplier' => $supplier,
            'mine' => $mine,
            'products' => $productsArray
            ]);
    }

    /**
     * Affichage de la vu pour ajouter un fournisseur
     * Et traitement de son formulaire
     *
     * @return string
     */
    public function add(): string
    {
        if ($this->supplier->find($_SESSION['auth']->getId(), 'user_id') &&
        ($_SESSION['auth']->getRole() != 1)) {
            $this->redirect();
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
                $this->redirect($url);
            }
        }

        return $this->render('supplier/add.html');
    }

    /**
     * Affichage de la vu pour modifier un fournisseur
     * Et traitement de son formulaire
     *
     * @return string
     */
    public function edit($slug, $id): string
    {
        $supplier = $this->supplier->find($id);
        if (!$supplier) {
            $this->redirect('/suppliers');
        }
        
        if ($_SESSION['auth']->getId() !== $supplier->getUserId()) {
            $this->redirect($this->generateUrl('supplier_show', ['slug' => $slug, 'id' => $id]));
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
                
                $this->redirect($this->generateUrl('supplier_show', ['slug' => $slug, 'id' => $id]));
            }
        }
        return $this->render('supplier/edit.html', ['supplier' => $supplier]);
    }
}
