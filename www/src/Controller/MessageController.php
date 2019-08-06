<?php
namespace App\Controller;

use Core\Controller\Controller;
use Core\Controller\FormController;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->userOnly();
        $this->loadModel('message');
        $this->loadModel('user');
    }

    /**
     * Affichage les messages recues de tous
     *
     * @return string
     */
    public function index($id)
    {
        $this->userOnlyById($id);
        
        $user = $this->user->find($id);
        $messages = $this->message->latestMessages($id);
        $users = $this->user->allWithoutMe($id);
        $sending = $this->message->findAll($id, 'sender_id');

        return $this->render('message/index.html', [
            'user' => $user,
            'messages' => $messages,
            'users' => $users,
            'sending' => $sending
        ]);
    }

    /**
     * Affichage les messages recues d'un autre utilisateur
     * Traitement de reponse
     *
     * @return string
     */
    public function show($id, $contact_id)
    {
        $this->userOnlyById($id);
        $messages = $this->message->findAllByContact($id, $contact_id);

        if (!$messages) {
            $this->redirect($this->generateUrl('messages', ['id' => $id]));
        }

        foreach ($messages as $value) {
            if ($value->getRead() == 0 && ($value->getReceiverId() == $_SESSION['auth']->getId())) {
                $this->message->itWasRead($value->getReceiverId());
            }
        }

        if (!empty($_POST)) {
            $_POST['sender_id'] = $id;
            $_POST['receiver_id'] = $contact_id;
            $fields = [];
            
            foreach ($_POST as $key => $value) {
                $fields[$key] = htmlspecialchars($value);
            }

            if ($this->message->create($fields)) {
                echo 'ok';
                die();
            } else {
                echo 'error';
                die();
            }
        }
        
        return $this->render('message/show.html', [
            'messages' => $messages,
            'user' => $this->user->find($id),
            'contact' => $this->user->find($contact_id)
        ]);
    }

    /**
     * Affichage un formulaire d'ecriture de message
     * Traitement de l'envoi
     *
     * @return string
     */
    public function new($id)
    {
        $this->userOnlyById($id);
        $users = $this->user->allWithoutMe($id);
        
        $form = new FormController();
        $form->field('receiver_id', ['require'])
            ->field('message', ['require', 'length' => 10]);
        $errors = $form->hasErrors();
        
        if (!isset($errors['post'])) {
            $datas = $form->getDatas();

            if (empty($errors)) {
                $datas['sender_id'] = $id;

                $this->message->create($datas);
                $this->flash()->addSuccess('Votre message a bien été envoyé');
                $this->redirect($this->generateUrl('message_show', [
                    'id' => $id,
                    'contact_id' => $datas['receiver_id']]));
            } else {
                $this->flash()->addAlert('Veillez à bien remplir les champs');
            }
        }

        return $this->render('message/add.html', [
            "users" => $users
        ]);
    }
}
