<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{

    public function indexAction()
    {
        if ($this->request->getPost()) {
            $escaper = new \App\components\MyEscaper();
            // echo '1 '.$this->request->getPost('email');
            $email = $escaper->sanitize($this->request->getPost('email'));
            $name = $escaper->sanitize($this->request->getPost('name'));
            $password = $escaper->sanitize($this->request->getPost('password'));
            // die($email);
            $user = new Users();
            try {
                $user->assign(
                    array('name' => $name, 'email' => $email, 'password' => $password),
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
                    $this->logger->getAdapter('signup')->begin();
                    $this->logger->error($user->getMessages());
                }
            } catch (Exception $e) {
                $this->logger
                    ->excludeAdapters(['login'])
                    ->error('This E-mail is already registered with us.');
                $this->response->setContent('This E-mail is already registered with us.');
            }
        }
    }
}
