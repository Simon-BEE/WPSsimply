<?php
namespace App\Model\Entity;

use Core\Model\Entity;

class CityEntity extends Entity
{
    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}
