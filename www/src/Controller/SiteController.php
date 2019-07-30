<?php
namespace App\Controller;

use Core\Controller\Controller;

class SiteController extends Controller
{
    public function index()
    {
        //dd($_SESSION["auth"]['google']['email']);
        return $this->render("site/index.html");
    }

    public function notices()
    {
        return $this->render('site/mentions.html');
    }

    public function notfound()
    {
        return $this->render('site/404.html');
    }
}