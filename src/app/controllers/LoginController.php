<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->cookies->has('remember-me')) {
            $email = $this->cookies->get('remember-me')->getValue();
            $this->response->redirect("login/loginByCookie/".$email."");
        }
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        if (!empty($email) or !empty($password)) {
            $user = Users::findFirst("email='" . $email . "' and password = '" . $password . "'");
            if ($user) {
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
                $this->response->redirect("index/dashboard");
            } else {
                $this->response->setStatusCode(403, 'Wrong credentials')
                ->setContent("Authentication Failed !!!!");
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
