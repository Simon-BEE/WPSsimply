<?php
namespace App\Model\Table;

use Core\Model\Table;

class ProductWarehouseTable extends Table
{
    public function existing($product, $warehouse)
    {
        if($this->query("SELECT * FROM {$this->table} WHERE product_id = $product AND warehouse_id = $warehouse")){
            return true;
        }else{
            return false;
        }
    }
}