<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{

    public function IndexAction()
    {
    }

    public function registerAction()
    {
        $user = new Users();
        $email = $this->request->getPost();
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email',
                'password'
            ]
        );

        $success = $user->save();
        $this->view->success = $success;

        if ($success) {
            $this->session->set('name', $user->name);
            $this->session->set('email', $user->email);
            $this->session->set('id', $user->user_id);
            $this->response->redirect("index/dashboard");
        } else {
            $this->view->message = "Not Register succesfully due to following reason: <br>" . implode("<br>", $user->getMessages());
        }
    }
}
