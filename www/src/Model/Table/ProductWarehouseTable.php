<?php
namespace App\Model\Table;

use Core\Model\Table;

class ProductWarehouseTable extends Table
{
    public function existing($product, $warehouse)
    {
        if ($this->query("SELECT * FROM {$this->table} WHERE product_id = $product AND warehouse_id = $warehouse")) {
            return true;
        } else {
            return false;
        }
    }

    public function whereNot($warehouse_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE NOT warehouse_id = $warehouse_id");
    }
}
