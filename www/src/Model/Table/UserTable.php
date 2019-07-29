<?php
namespace App\Model\Table;

use Core\Model\Table;
use App\Model\Entity\UserEntity;

class UserTable extends Table
{
    public function getUser($mail, $password)
    {
        $user = $this->query("SELECT * FROM $this->table 
            WHERE mail = ?", [$mail], true);
        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                $user->setPassword('');
                return $user;
            }
        }
        return false;
    }

    public function getUserByid($id)
    {
        return $this->query("SELECT * FROM $this->table
        WHERE $this->table.id = ?", [$id], true);
    }
    
    public function newUser(array $datas): bool
    {
        $sqlParts = [];
        foreach ($datas as $nom => $value) {
            $sqlParts[] = "$nom = :$nom";
        }
        $statement = "INSERT INTO {$this->table} SET ".join(', ', $sqlParts);
        return $this->query($statement, $datas);
    }

    public function delete($id, $column = 'id', UserEntity $user)
    {
        if ($user->getRole() == 1) {
            $this->query("DELETE FROM supplier WHERE user_id = '$id'");
        }elseif ($user->getRole() == 2){
            $this->query("DELETE FROM warehouse WHERE user_id = '$id'");
        }
        
        return $this->query("DELETE FROM {$this->table} WHERE $column = '$id'");
    }
}