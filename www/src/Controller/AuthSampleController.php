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
                header('location: /google');
                exit();
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
        if ($_SESSION['google'] == null) {
            header('location: /');
            exit();
        }

        $email = $_SESSION["google"]["email"];
        $name = $_SESSION['google']['givenName'] . " " . $_SESSION['google']['familyName'];
        
        if ($this->user->find($email, 'mail')) {
            $user = $this->user->find($email, 'mail');
            $_SESSION["auth"] = $user;
            header('location: /profile');
            exit();
        } else {
            $form = new FormController();
            $form->field('password', ["require", "verify", "length" => 6])
                ->field('name', ['require'])
                ->field('role', ['require']);
            $errors =  $form->hasErrors();
            
            if (!isset($errors["post"])) {
                $datas = $form->getDatas();
                if (empty($errors)) {
                    $datas['mail'] = $email;
                    $datas["password"] = password_hash($datas["password"], PASSWORD_BCRYPT);
                    $this->user->newUser($datas);

                    $this->flash()->addSuccess("Vous êtes bien enregistré");
                    $user = $this->user->find($this->user->last());
                    $_SESSION['auth'] = $user;
                    header('location: ' . $this->generateUrl("profile"));
                    exit();
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
            'default_graph_version' => 'v2.10',
            ]);
            
        return $fb;

    }

    /**
     * Gère la connexion par Facebook avec le nom d'utilisateur (non securisée)
     * Redirige selon les informations données
     * @return void
     */
    public function facebook(): void
    {
        $accessToken = $_SESSION['facebook_access_token'];
        $fb = AuthController::loginByFacebook();
        
        $response = $fb->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
        $userData = $response->getGraphNode()->asArray();
        $_SESSION['userData'] = $userData;

        if (!$_SESSION['userData']) {
            unset($_SESSION);
            header('location: /');
            exit();
        }

        $name = $_SESSION['userData']['first_name'] . ' ' . $_SESSION['userData']['last_name'];

        if ($this->user->find($name, 'name')) {
            $user = $this->user->find($name, 'name');
            $_SESSION['auth'] = $user;
            unset($_SESSION['FBRLH_state']);
            unset($_SESSION['facebook_access_token']);
            unset($_SESSION['userData']);
            $this->flash()->addSuccess('Vous êtes connecté !');
            header('location: /profile');
            exit();

        }else{
            unset($_SESSION['FBRLH_state']);
            unset($_SESSION['facebook_access_token']);
            unset($_SESSION['userData']);
            $this->flash()->addAlert('Veuillez vous enregistrer avant de vous connecter !');
            header('location: /register');
            exit();
        }
    }
}
