<?php
namespace App\Model\Entity;

use Core\Model\Entity;

class MessageEntity extends Entity
{
    private $id;
    private $sender_id;
    private $message;
    private $receiver_id;
    private $read;
    private $created_at;

    public function getId()
    {
        return $this->id;
    }

    public function getSenderId()
    {
        return $this->sender_id;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getReceiverId()
    {
        return $this->receiver_id;
    }

    public function getRead()
    {
        return $this->read;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setSenderId($sender_user_id)
    {
        $this->sender_user_id = $sender_user_id;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setReceiverId($receiver_user_id)
    {
        $this->receiver_user_id = $receiver_user_id;
    }
}
