<?php
namespace App\Controller;

use \App\Model\Table\UserTable;
use \Core\Controller\Controller;
use App\Controller\AuthController;
use \Core\Controller\URLController;
use Core\Controller\FormController;
use \Core\Controller\MailController;

class UserController extends Controller
{
    /**
     * Récupère les tables user et supplier
     */
    public function __construct()
    {
        $this->loadModel('user');
        $this->loadModel('supplier');
        $this->loadModel('warehouse');
    }

    /**
     * Affichage de la vu de connexion
     * et du traitement du formulaire de connexion
     *
     * @return string
     */
    public function login(): string
    {
        $this->userForbidden();
        
        unset($_SESSION["google"]);
        $google = new AuthController();
        $googleClient = $google->loginByGoogle();
        

        if (!$_SESSION["access_token"]) {
            $facebookClient = AuthController::loginByFacebook();
            $facebookClientUrl = $facebookClient->getRedirectLoginHelper()
                ->getLoginUrl('http://localhost:8030/facebook-login');
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
                    $this->redirect('/profile');
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
            'googleUrl' => $googleClient->createAuthUrl(),
            'facebookUrl' => $facebookClientUrl
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
        $this->userForbidden();

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

                $this->redirect($this->generateUrl("login"));
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
        $this->userOnly();
        
        $user = $_SESSION['auth'];

        if (!$user) {
            $this->redirect('/');
        }

        if ($this->supplier->find($user->getId(), 'user_id')) {
            $supplier = $this->supplier->find($user->getId(), 'user_id');
        } elseif ($this->warehouse->find($user->getId(), 'user_id')) {
            $warehouse = $this->warehouse->find($user->getId(), 'user_id');
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

        return $this->render('user/profile.html', [
            'user' => $user,
            'supplier' => $supplier,
            'warehouse' => $warehouse]);
    }

    /**
     * Methode pour se déconnecter
     *
     * @return void
     */
    public function logout():void
    {
        unset($_SESSION['auth']);
        unset($_SESSION['google']);
        $this->redirect();
    }
}
