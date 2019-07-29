<?php
namespace App\Model\Table;

use Core\Model\Table;

class SupplierTable extends Table
{
    public function delete($id, $column = 'id')
    {
        $this->query("DELETE FROM product WHERE supplier_id = $id");
        return $this->query("DELETE FROM {$this->table} WHERE $column = ?", [$id], true);
    }
}