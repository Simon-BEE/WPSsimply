<?php
namespace Tests\Core\Controller;

use \PHPUnit\Framework\TestCase;
use \Core\Controller\FormController;

class TextControllerTest extends TestCase
{
    public function testNewWithPost()
    {
        $_POST = ["name"=> "julien"];

        $form = new FormController();
        $this->assertEquals([], $form->hasErrors());
    }

    public function testNewWithoutPost()
    {
        $_POST = [];
        $form = new FormController();
        $errors = $form->hasErrors();
        $this->assertArrayHasKey("post", $errors);
        $this->assertEquals(['no-data'], $errors["post"]);
    }

    public function testGetDatasWithoutFields()
    {
        $_POST = ["name"=> "julien"];
        $form = new FormController();
        $this->assertEquals([], $form->getDatas());
    }

    public function testGetDatasWithFields()
    {
        $_POST = ["firstname"=> "julien", "lastname"=> "dugrais"];
        $form = new FormController();
        $form->field("lastname")
            ->field('firstname');
        $this->assertEquals(
            ["firstname"=> "julien", "lastname"=> "dugrais"],
            $form->getDatas()
        );
    }

    public function testFieldsChained()
    {
        $_POST = ["firstname"=> "julien", "lastname"=> "dugrais"];
        $form = new FormController();
        $this->assertEquals($form, $form->field("lastname"));
    }

    
    public function testGetDatasAndErrorsWithFieldsRequire()
    {
        $_POST = ["firstname"=> "julien"];
        $form = new FormController();
        $form->field("firstname", ["require"]);
        $this->assertEquals(["firstname"=> "julien"], $form->getDatas());
        $this->assertEquals([], $form->hasErrors());
    }

    public function testGetDatasAndErrorsWithBlankFieldsRequire()
    {
        $_POST = ["firstname"=> ""];
        $form = new FormController();
        $form->field("firstname", ["require"]);
        $this->assertEquals([], $form->getDatas());
        $errors =  $form->hasErrors();
        $this->assertArrayHasKey("firstname", $errors);
        $this->assertEquals(["le champ firstname est requis"], $errors["firstname"]);
    }

    public function testGetDatasAndErrorsWithFieldsVerify()
    {
        $_POST = ["firstname"=> "julien", "firstnameVerify"=> "julien"];
        $form = new FormController();
        $form->field("firstname", ["verify"]);
        $this->assertEquals(["firstname"=> "julien"], $form->getDatas());
        $this->assertEquals([], $form->hasErrors());
    }

    public function testGetDatasAndErrorsWithBlankFieldsVerify()
    {
        $_POST = ["firstname"=> "julien", "firstnameVerify"=> "pierre"];
        $form = new FormController();
        $form->field("firstname", ["verify"]);
        $this->assertEquals([], $form->getDatas());
        $errors =  $form->hasErrors();
        $this->assertArrayHasKey("firstname", $errors);
        $this->assertEquals(
            ["les champs firstname doivent correspondre"],
            $errors["firstname"]
        );
    }
    
    public function testGetDatasAndErrorsWithFieldsLength()
    {
        $_POST = ["password"=> "julien87"];
        $form = new FormController();
        $form->field("password", ["length"=> 8]);
        $this->assertEquals(["password"=> "julien87"], $form->getDatas());
        $this->assertEquals([], $form->hasErrors());
    }

    public function testGetDatasAndErrorsWithBlankFieldsLength()
    {
        $_POST = ["password"=> "julien"];
        $form = new FormController();
        $form->field("password", ["length"=> 8]);
        $this->assertEquals([], $form->getDatas());
        $errors =  $form->hasErrors();
        $this->assertArrayHasKey("password", $errors);
        $this->assertEquals(
            ["le champ password doit avoir au minimum 8 caractères"],
            $errors["password"]
        );
    }

    public function testGetDatasAndErrorsWithFieldsConstraints()
    {
        $_POST = ["password"=> "julien87"];
        $form = new FormController();
        $form->field("password", ["require", "length"=> 8]);
        $this->assertEquals(["password"=> "julien87"], $form->getDatas());
        $this->assertEquals([], $form->hasErrors());
    }

    public function testGetDatasAndErrorsWithBlankFieldsConstraints()
    {
        $_POST = ["password"=> "julien"];
        $form = new FormController();
        $form->field("password", ["require", "length"=> 8]);
        $this->assertEquals([], $form->getDatas(), "le retour data doit etre vide");
        $errors =  $form->hasErrors();
        $this->assertArrayHasKey("password", $errors);
        $this->assertEquals(
            ["le champ password doit avoir au minimum 8 caractères"],
            $errors["password"]
        );
    }

    public function testGetDatasAndErrorsWithMultipleFieldsConstraints()
    {
        $_POST = ["password"=> ""];
        $form = new FormController();
        $form->field("password", ["require", "length"=> 8]);
        $this->assertEquals([], $form->getDatas());
        $errors =  $form->hasErrors();
        $this->assertArrayHasKey("password", $errors);
        $this->assertContains(
            "le champ password est requis",
            $errors["password"]
        );
        $this->assertContains(
            "le champ password doit avoir au minimum 8 caractères",
            $errors["password"]
        );
    }

    public function testConstraintNotExist()
    {
        $_POST = ["lastname"=> "julien"];
        $form = new FormController();
        $form->field("lastname", ["trucQuiExistePas"]);
        
        $this->expectExceptionMessage("la contrainte trucQuiExistePas n'existe pas");
        
        $form->hasErrors();
    }
}
