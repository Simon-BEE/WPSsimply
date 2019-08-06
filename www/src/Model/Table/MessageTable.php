<?php
namespace App\Model\Table;

use Core\Model\Table;

class MessageTable extends Table
{
    public function findAllByContact($sender_id, $receiver_id)
    {
        return $this->query("SELECT * FROM {$this->table} 
            WHERE (sender_id = $sender_id AND receiver_id = $receiver_id) 
            OR (receiver_id = $sender_id AND sender_id = $receiver_id)");
    }

    public function latestMessages($sender_id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE receiver_id = $sender_id ORDER BY id DESC");
    }

    public function itWasRead($id)
    {
        return $this->query("UPDATE {$this->table} SET `read` = 1 WHERE receiver_id = $id");
    }

    public function msgStandBy($sender_id)
    {
        return $this->query("SELECT * FROM {$this->table} 
            WHERE (sender_id = $sender_id AND receiver_id != $sender_id)");
    }
}
