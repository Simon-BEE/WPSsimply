<?php
namespace App\Controller\Admin;

use Core\Controller\Controller;

class AdminController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['auth']) || $_SESSION['auth']->getRole() != 7) {
            header('location: /');
            exit();
        }

        return $this->render('admin/index.html');
    }
}