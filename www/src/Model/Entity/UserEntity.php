<?php
namespace App\Model\Entity;

use Core\Model\Entity;

class UserEntity extends Entity
{
    private $id;
    private $mail;
    private $name;
    private $password;
    private $role;

    
    public function getId()
    {
        return $this->id;
    }

    public function getMail()
    {
        return $this->mail;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setRole(string $role)
    {
        $this->role = $role;
    }

    public function setPassword(string $password)
    {
        $password = password_hash(htmlspecialchars($password), PASSWORD_BCRYPT);
        $this->password = $password;
    }
}