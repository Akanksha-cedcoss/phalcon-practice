<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class LoginController extends Controller
{
    
    /**
     * log in user
     *
     * @return void
     */
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
                $this->loginLogger->error('Incorrect credentials entered by user.');
                $this->response->setStatusCode(403, 'Wrong credentials')
                    ->setContent("Authentication Failed !!!!");
            }
        }
    }

    /**
     * log in user if cookie is set
     *
     * @param [type] $email
     * @return void
     */
    public function loginByCookieAction($email)
    {
        $user = Users::findFirst("email='" . $email . "'");
        $session = $this->di->get('session');
        $session->set('name', $user->name);
        $session->set('email', $user->email);
        $session->set('id', $user->user_id);
        $this->response->redirect("index/dashboard");
    }

    /**
     * logout user
     *
     * @return void
     */
    public function logoutAction()
    {
        $this->di->get('session')->destroy();
        $this->cookies->get('remember-me')->delete();
        $this->response->redirect("index/index");
    }
}
