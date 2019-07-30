<?php
namespace App\Controller;

use Core\Controller\Controller;

class SiteController extends Controller
{
    /**
     * Affichage de la vu de la page home
     *
     * @return string
     */
    public function index():string
    {
        return $this->render("site/index.html");
    }

    /**
     * Affichage de la vu de la page des mentions legales
     *
     * @return string
     */
    public function notices():string
    {
        return $this->render('site/mentions.html');
    }

    /**
     * Affichage de la vu de la page 404
     *
     * @return string
     */
    public function notfound():string
    {
        return $this->render('site/404.html');
    }
}
