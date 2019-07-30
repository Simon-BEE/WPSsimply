<?php
namespace App\Model\Entity;

use Core\Model\Entity;
use Cocur\Slugify\Slugify;

class ProductEntity extends Entity
{
    private $id;
    private $name;
    private $supplier_id;
    private $toxicity;
    private $volume;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    public function getToxicity()
    {
        return $this->toxicity;
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function setName($name)
    {
        return $this->name = $name;
    }

    public function setSupplierId($supplier_id)
    {
        return $this->supplier_id = $supplier_id;
    }

    public function setToxicity($toxicity)
    {
        return $this->toxicity = $toxicity;
    }

    public function setVolume($volume)
    {
        return $this->volume = $volume;
    }

    public function getUrl()
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($this->getName());
        return \App\App::getInstance()->getRouter()->url(
            'product_show',
            [
                'slug' => $slug, 'id' => $this->getId()
            ]
        );
    }
}
