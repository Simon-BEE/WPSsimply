<?php
namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\URLController;
use \Core\Controller\MailController;
use \App\Model\Table\UserTable;
use Core\Controller\FormController;

class UserController extends Controller
{
    public function __construct()
    {
        $this->loadModel('user');
        $this->loadModel('supplier');
    }

    /**
     * Affichage de la vu de connexion 
     * et du traitement du formulaire de connexion
     *
     * @return string
     */
    public function login(): string
    {
        $form = new FormController();
        $form->field('mail', ["require"])
            ->field('password', ["require"]);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            
            if (empty($errors)) {
                $user = $this->user->getUser($datas["mail"], $datas["password"]);
                if ($user) {
                    $this->flash()->addSuccess("le POST est super top");
                    $_SESSION['auth'] = $user;
                    header('location: /profile');
                } else {
                    $this->flash()->addAlert("pas cool");
                }
            } else {
                $this->flash()->addAlert("appprend a remplir un formulaire");
            }
            unset($datas['password']);
        }

        return $this->render('user/login.html', compact("datas"));
    }

    /**
     * Affichage de la vu d'inscription 
     * et du traitement du formulaire inscription
     *
     * @return string
     */
    public function register(): string
    {
        $form = new FormController();
        $form->field('mail', ["require", "verify"])
            ->field('password', ["require", "verify", "length" => 6])
            ->field('name', ['require'])
            ->field('role', ['require']);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $userTable = $this->user;
                if ($userTable->find($datas["mail"], "mail")) {
                    throw new \Exception("utilisateur existe deja");
                }

                $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);
                if (!$userTable->newUser($datas)) {
                    throw new \Exception("erreur de base de donné");
                }

                $this->flash()->addSuccess("vous êtes bien enregistré");
                // $mail = new MailController();
                // $mail->object("validez votre compte")
                //     ->to($datas["mail"])
                //     ->message('confirmation', compact("datas"))
                //     ->send();
                // $this->flash()->addSuccess("vous avez reçu un mail");

                header('location: ' . $this->generateUrl("login"));
                exit();
            }
            die('ici');
            unset($datas["password"]);

        } else {
            unset($errors);
        }

        return $this->render('user/register.html', compact("errors", "datas"));
    }

    public function profile()
    {
        if (!$_SESSION['auth']) {
            \header('location: /');
        }
        $user = $_SESSION['auth'];

        if ($this->supplier->find($user->getId(), 'user_id')) {
            $supplier = $this->supplier->find($user->getId(), 'user_id');
        }
        
        return $this->render('user/profile.html', ['user' => $user, 'supplier' => $supplier]);
    }

    public function logout()
    {
        unset($_SESSION['auth']);
        header('location: /');
    }
}