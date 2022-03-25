<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{

    public function indexAction()
    {
        if ($this->request->getPost()) {
            $email = $this->escaper->escapeHtml($this->request->getPost('email'));
            $name = $this->escaper->escapeHtml($this->request->getPost('name'));
            $password = $this->escaper->escapeHtml($this->request->getPost('password'));
            $user = new Users();
            try {
                $user->assign(
                    array('name'=>$name, 'email'=>$email, 'password'=>$password),
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
                    $this->response->redirect("index/dashboard");
                } else {
                    $this->response->setContent($user->getMessages());
                }
            } catch (Exception $e) {
                $this->response->setContent("E-mail is already registered with us.");
            }
        }
    }
}
