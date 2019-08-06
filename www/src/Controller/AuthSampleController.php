<?php
namespace App\Controller;

use Core\Controller\Controller;
use Core\Controller\FormController;
use Google_Client;

class SAMPLE extends Controller
{
    /**
     * Récupère la table user
     */
    public function __construct()
    {
        $this->loadModel('user');
    }

    /**
     * Gère la connexion par Google
     *
     * @return Google_Client
     */
    public function loginByGoogle(): Google_Client
    {
        $url = 'http://localhost:8030/login';
        
        $g_client = new \Google_Client();
        $g_client->setClientId(/* METTRE SON CLIENT ID*/);
        $g_client->setClientSecret(/* METTRE SON SECRET CLIENT */);
        $g_client->setRedirectUri($url);
        $g_client->setScopes("email");
        $g_client->addScope("profile");
        
        $code = isset($_GET['code']) ? $_GET['code'] : null;
        if (isset($code)) {
            try {
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                $g_client->setAccessToken($token);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
            try {
                $g_client->verifyIdToken();
                $oAuth = new \Google_Service_Oauth2($g_client);
                $userData = $oAuth->userinfo_v2_me->get();
                $_SESSION['google'] = $userData;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            if ($_SESSION['google']) {
                $this->redirect('/google');
            }
        }
        
        return $g_client;
    }

    /**
     * Redirige l'utilisateur connecté avec Google selon s'il est déjà en BDD ou pas
     * Traite le formulaire si aucune connaissance de son email dans la BDD
     *
     * @return string
     */
    public function google(): string
    {
        if (($_SESSION['google'] == null) || empty($_SESSION['userData'])) {
            if (empty($_SESSION['userData'])) {
                $this->redirect();
            }
        }

        if ($_SESSION['google']) {
            $email = $_SESSION["google"]["email"];
            $name = $_SESSION['google']['givenName'] . " " . $_SESSION['google']['familyName'];
        } else {
            $name = $_SESSION['userData']['first_name'] . " " . $_SESSION['userData']['last_name'];
            $email = $_SESSION['userData']['email'];
        }
        
        if ($this->user->find($email, 'mail')) {
            $user = $this->user->find($email, 'mail');
            $_SESSION["auth"] = $user;
            
            $this->redirect('/profile');
        } else {
            $form = new FormController();
            $form->field('password', ["require", "verify", "length" => 6])
                ->field('name', ['require'])
                ->field('role', ['require']);
            if (!$email) {
                $form->field('mail', ['require']);
            }
            $errors =  $form->hasErrors();
            
            if (!isset($errors["post"])) {
                $datas = $form->getDatas();
                if (empty($errors)) {
                    if ($email) {
                        $datas['mail'] = $email;
                    }
                    
                    $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);
                    $this->user->newUser($datas);

                    $this->flash()->addSuccess("Vous êtes bien enregistré");
                    $user = $this->user->find($this->user->last());
                    $_SESSION['auth'] = $user;
                    
                    $this->redirect($this->generateUrl("profile"));
                } else {
                    $this->flash()->addAlert('Veillez à bien remplir tous les champs');
                }
                unset($datas["password"]);
            } else {
                unset($errors);
            }
            return $this->render('user/google.html', ['mail' => $email, 'name' => $name]);
        }
    }
    
    /**
     * Création d'un objet Facebook
     * @return Object
     */
    public static function loginByFacebook(): Object
    {
        $fb = new \Facebook\Facebook([
            'app_id' => /* id de l'application */,
            'app_secret' => /* secret id de l'application */,
            'default_graph_version' => 'v4.0',
            ]);
            
        return $fb;
    }

    /**
     * Gère la connexion par Facebook avec le nom d'utilisateur (non securisée)
     * Redirige selon les informations données
     * @return void
     */
    public function facebook()
    {
        $fb = AuthController::loginByFacebook();
        $tok = $fb->getRedirectLoginHelper()->getAccessToken();
        
        $response = $fb->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $tok);
        $userData = $response->getGraphNode()->asArray();
        $_SESSION['userData'] = $userData;
        
        if (!$_SESSION['userData']) {
            unset($_SESSION);
            $this->redirect();
        }

        $mail = $_SESSION['userData']['email'];

        if ($this->user->find($mail, 'mail')) {
            $user = $this->user->find($mail, 'mail');
            $_SESSION['auth'] = $user;
            unset($_SESSION['userData']);
            $this->flash()->addSuccess('Vous êtes connecté !');
            $this->redirect('/profile');
        } else {
            $this->flash()->addAlert('Veuillez vous enregistrer avant de vous connecter !');
            $this->redirect('/google');
        }
    }
}
