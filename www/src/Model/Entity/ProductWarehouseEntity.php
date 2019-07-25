<?php
namespace App\Model\Entity;

use Core\Model\Entity;

class ProductWarehouseEntity extends Entity
{
    private $product_id;
    private $warehouse_id;

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getWarehouseId()
    {
        return $this->warehouse_id;
    }
}