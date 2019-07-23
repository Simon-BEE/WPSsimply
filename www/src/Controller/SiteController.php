<?php
namespace App\Controller;

use Core\Controller\Controller;

class SiteController extends Controller
{
    public function index()
    {
        return $this->render("site/index.html");
    }
}