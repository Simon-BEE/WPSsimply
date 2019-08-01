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
        $messages = $this->message->findAll($id, 'receiver_id');
        $users = $this->user->allWithoutMe($id);


        return $this->render('message/index.html',[
            'user' => $user,
            'messages' => $messages,
            'users' => $users
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
        // $myMessages = $this->message->findMyMessage($id, $contact_id);
        // $contactMessages = $this->message->findContactMessage($contact_id, $id);

        // dump($myMessages);
        // dd($contactMessages);
        // if (!$myMessages && !$contactMessages) {
        //     header('location: '.$this->generateUrl('messages', ['id' => $id]));
        // }
        
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
                header('location: '. $this->generateUrl('message_show', ['id' => $id, 'contact_id' => $datas['receiver_id']]));
                exit();
            }else{
                $this->flash()->addAlert('Veillez à bien remplir les champs');
            }
        }

        return $this->render('message/add.html', [
            "users" => $users
        ]);
    }
}