<?php
namespace App\Model\Table;

use Core\Model\Table;

class ProductTable extends Table
{
    public function delete($id, $column = 'id')
    {
        $this->query("DELETE FROM product_warehouse WHERE product_id = '$id'");
        return $this->query("DELETE FROM {$this->table} WHERE $column = '$id'");
    }
}