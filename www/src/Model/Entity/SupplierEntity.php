<?php
namespace App\Model\Entity;

use Core\Model\Entity;
use Cocur\Slugify\Slugify;

class SupplierEntity extends Entity
{
    private $id;
    private $social;
    private $address;
    private $user_id;

    public function getId()
    {
        return $this->id;
    }

    public function getSocial()
    {
        return $this->social;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setSocial($social)
    {
        return $this->social = $social;
    }

    public function setAddress($address)
    {
        return $this->address = $address;
    }

    public function setUserId($user_id)
    {
        return $this->user_id = $user_id;
    }

    public function getUrl()
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->getSocial());
        return \App\App::getInstance()->getRouter()->url(
            'supplier_show', [
                'slug' => $slug, 'id' => $this->getId()
            ]);
    }
}