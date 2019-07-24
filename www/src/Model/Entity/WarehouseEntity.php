<?php
namespace App\Model\Entity;

use Core\Model\Entity;

class WarehouseEntity extends Entity
{
    private $id;
    private $name;
    private $city_id;
    private $surface;
    private $address;
    private $user_id;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCityId()
    {
        return $this->city_id;
    }

    public function getSurface()
    {
        return $this->surface;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function setSurface($surface)
    {
        return $this->surface = $surface;
    }

    public function setAddress($address)
    {
        return $this->address = $address;
    }

    public function setUserId($user_id)
    {
        return $this->user_id = $user_id;
    }
}