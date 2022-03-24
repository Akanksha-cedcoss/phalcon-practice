<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        if ($this->cookies->has('remember-me')) {
            $email = $this->cookies->get('remember-me')->getValue();
            $this->response->redirect("login/loginByCookie/".$email."");
        }
        

        // return '<h1>Hello World!</h1>';
    }
    public function dashboardAction()
    {
        if (!($this->session->has('id') or $this->cookies->has('remember-me'))) {
            $this->response->redirect("index/index");
        }
    }
}
