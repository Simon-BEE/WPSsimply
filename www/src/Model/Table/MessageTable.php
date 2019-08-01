<?php
namespace App\Model\Table;

use Core\Model\Table;

class MessageTable extends Table
{
    public function findAllByContact($sender_id, $receiver_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE (sender_id = $sender_id AND receiver_id = $receiver_id) 
            OR (receiver_id = $sender_id AND sender_id = $receiver_id)");
    }
    
}