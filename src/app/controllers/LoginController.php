<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class LoginController extends Controller
{
    public function indexAction()
    {
        //return '<h1>Hello!!!</h1>';
    }
    public function loginAction()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        if (empty($email) or empty($password)) {
            $response = new Response(
                "Sorry, Authentication failed",
                404,
                'Error'
            );
            if (true !== $response->isSent()) {
                $response->send();
            }
            return;
        } else {
            $user = Users::findFirst("email='" . $email . "'");
            if ($user) {
                if ($user->password == $password) {
                    $this->session->set('name', $user->name);
                    $this->session->set('email', $user->email);
                    $this->session->set('id', $user->user_id);
                    if ($this->request->getPost('remember') == '1') {
                        $this->cookies->set(
                            'remember-me',
                            $user->email,
                            time() + 15 * 86400
                        );
                        $this->cookies->send();
                    }
                    $this->flash->error('succ');
                    $this->response->redirect("index/dashboard");
                } else {
                    $response = new Response(
                        "Sorry, Authentication failed",
                        404,
                        'Error'
                    );
                    if (true !== $response->isSent()) {
                        $response->send();
                    }
                }
            } else {
                $response = new Response(
                    "Sorry, Authentication failed",
                    404,
                    'Error'
                );
                if (true !== $response->isSent()) {
                    $response->send();
                }
            }
        }
    }
    public function loginByCookieAction($email)
    {
        $user = Users::findFirst("email='" . $email . "'");
        $this->session->set('name', $user->name);
        $this->session->set('email', $user->email);
        $this->session->set('id', $user->user_id);
        $this->response->redirect("index/dashboard");
    }
    public function logoutAction()
    {
        $this->session->destroy();
        $this->cookies->get('remember-me')->delete();
        $this->response->redirect("index/index");
    }
}
