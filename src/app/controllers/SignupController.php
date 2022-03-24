<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{

    public function indexAction()
    {
        if ($this->request->getPost()) {
            $email = $this->request->getPost('email');
            $user = new Users();
            try {
                $user->assign(
                    $this->request->getPost(),
                    [
                        'name',
                        'email',
                        'password'
                    ]
                );
                $user->save();
                if ($user) {
                    $session = $this->di->get('session');
                    $session->set('name', $user->name);
                    $session->set('email', $user->email);
                    $session->set('id', $user->user_id);
                    header("location:../index");
                } else {
                    $this->response->setContent($user->getMessages());
                }
            } catch (Exception $e) {
                $this->response->setContent("E-mail is already registered with us.");
            }
        }
    }
}
