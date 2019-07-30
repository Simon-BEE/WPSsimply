<?php
namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\URLController;
use \Core\Controller\MailController;
use \App\Model\Table\UserTable;
use Core\Controller\FormController;

class UserController extends Controller
{
    /**
     * Récupère les tables user et supplier
     */
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

        if (!$_SESSION["auth"]["google"]["email"]) {
            $google = new AuthController();
            $googleClient = $google->loginByGoogle();
        }

        $form = new FormController();
        $form->field('mail', ["require"])
            ->field('password', ["require"]);
        $errors =  $form->hasErrors();
        
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            
            if (empty($errors)) {
                $user = $this->user->getUser($datas["mail"], $datas["password"]);
                if ($user) {
                    $this->flash()->addSuccess("Vous êtes bien connecté");
                    $_SESSION['auth'] = $user;
                    header('location: /profile');
                } else {
                    $this->flash()->addAlert("L'adresse email et/ou le mot de passe est/son incorrect/s");
                }
            } else {
                $this->flash()->addAlert("Le formulaire est map rempli");
            }
            unset($datas['password']);
        }
        
        return $this->render('user/login.html', [
            'datas' => $datas,
            'googleUrl' => $googleClient->createAuthUrl()
            ]);
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
                    throw new \Exception("L'tilisateur existe déjà");
                }

                $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);
                if (!$userTable->newUser($datas)) {
                    throw new \Exception("Erreur en base de données, veuillez réessayer ultérieurement");
                }

                $this->flash()->addSuccess("Vous êtes bien enregistré");

                header('location: ' . $this->generateUrl("login"));
                exit();
            }
            unset($datas["password"]);
        } else {
            unset($errors);
        }

        return $this->render('user/register.html', compact("errors", "datas"));
    }

    /**
     * Affichage de la vu du profil
     * et traitement de modifications de ses infos perso
     *
     * @return string
     */
    public function profile():string
    {
        if (!$_SESSION['auth']) {
            \header('location: /');
        }
        
        $user = $_SESSION['auth'];

        if ($this->supplier->find($user->getId(), 'user_id')) {
            $supplier = $this->supplier->find($user->getId(), 'user_id');
        }

        $form = new FormController();
        $form->field('mail', ['require'])
            ->field('password', ["verify", "length" => 6])
            ->field('name', ['require']);
        $errors =  $form->hasErrors();
    
        if (!isset($errors["post"])) {
            $datas = $form->getDatas();
            if (empty($errors)) {
                $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);

                if (!$this->user->update($user->getId(), 'id', $datas)) {
                    throw new \Exception("Erreur en base de données, veuillez réessayer ultérieurement");
                }

                $this->flash()->addSuccess('Vos informations ont bien été mises à jour');
                $user = $this->user->getUserByid($user->getId());
            } else {
                $this->flash()->addAlert('Veuillez remplir tous les champs correctement !');
            }
        }
        return $this->render('user/profile.html', ['user' => $user, 'supplier' => $supplier]);
    }

    /**
     * Methode pour se déconnecter
     *
     * @return void
     */
    public function logout():void
    {
        unset($_SESSION['auth']);
        header('location: /');
    }
}
