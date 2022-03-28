<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->cookies->has('remember-me')) {
            $email = $this->cookies->get('remember-me')->getValue();
            $this->response->redirect("login/loginByCookie/" . $email . "");
        }
        $escaper = new \App\components\MyEscaper();
        $email = $escaper->sanitize($this->request->getPost('email'));
        $password = $escaper->sanitize($this->request->getPost('password'));
        if (!empty($email) or !empty($password)) {
            $user = Users::findFirst("email='" . $email . "' and password = '" . $password . "'");
            if ($user) {
                $session = $this->di->get('session');
                $session->set('name', $user->name);
                $session->set('email', $user->email);
                $session->set('id', $user->user_id);
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
                $this->logger
                    ->excludeAdapters(['signup'])
                    ->error('E-mail or password is incorrect');
                $this->response->setStatusCode(403, 'Wrong credentials')
                    ->setContent("Authentication Failed !!!!");
            }
        }
    }
    public function loginByCookieAction($email)
    {
        $user = Users::findFirst("email='" . $email . "'");
        $session = $this->di->get('session');
        $session->set('name', $user->name);
        $session->set('email', $user->email);
        $session->set('id', $user->user_id);
        $this->response->redirect("index/dashboard");
    }
    public function logoutAction()
    {
        $this->di->get('session')->destroy();
        $this->cookies->get('remember-me')->delete();
        $this->response->redirect("index/index");
    }
}
