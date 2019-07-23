<?php
namespace Core\Controller;

class FormController
{

    private $postDatas;

    private $fields = [];

    private $errors = [];

    private $datas = [];

    private $isVerify = false;

    public function __construct()
    {
        if (count($_POST) > 0) {
            $this->postDatas = $_POST;
        } else {
            $this->addError("post", "no-data");
        }
    }

    public function field(string $field, array $constraints = []): self
    {
        
        foreach ($constraints as $constraint => $value) {
            if (!is_string($constraint)) {
                $constraints[$value] = true;
                unset($constraints[$constraint]);
            }
        }
        $this->fields[$field] =  $constraints;
        return $this;
    }
    

    public function hasErrors(): array
    {
        $this->verifyErrors();
        return $this->errors;
    }

    public function getDatas(): array
    {
        $this->verifyErrors();
        return $this->datas;
    }

    private function verifyErrors(): void
    {
        if (!$this->isVerify) {
            foreach ($this->fields as $field => $constraints) {
                if (count($constraints) <= 0) {
                    $this->addData($field);
                }
                foreach ($constraints as $constraint => $value) {
                    $constraintMethod = 'error'.ucfirst(strtolower($constraint));
                    if (method_exists($this, $constraintMethod)) {
                        $this->$constraintMethod($field, $value);
                    } else {
                        throw new \Exception("la contrainte {$constraint} n'existe pas");
                    }
                }
            }
            $this->isVerify = true;
        }
    }

    private function errorRequire(string $field): void
    {
        if (!empty($this->postDatas[$field])) {
            $this->addData($field);
        } else {
            $this->addError($field, "le champ {$field} est requis");
        }
    }

    private function errorVerify(string $field): void
    {
        if (isset($this->postDatas[$field."Verify"])) {
            if ($this->postDatas[$field."Verify"] == $this->postDatas[$field]) {
                $this->addData($field);
            } else {
                $this->addError($field, "les champs {$field} doivent correspondre");
            }
        }
    }

    private function errorLength(string $field, $value): void
    {
        if (strlen($this->postDatas[$field]) >= $value) {
            $this->addData($field);
        } else {
            $this->addError($field, "le champ {$field} doit avoir au minimum {$value} caractères");
        }
    }




    private function addData(string $field): void
    {
        if (!isset($this->errors[$field])) {
            $this->datas[$field] = htmlspecialchars($this->postDatas[$field]);
        }
    }

    private function addError(string $field, string $message): void
    {
        unset($this->datas[$field]);
        $this->errors[$field][] = $message;
    }
}
