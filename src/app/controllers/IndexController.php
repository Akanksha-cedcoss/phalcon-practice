<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        
        

        // return '<h1>Hello World!</h1>';
    }
    public function dashboardAction()
    {
        if (!($this->session->has('id') or $this->cookies->has('remember-me'))) {
            $this->response->redirect("index/index");
        }
    }
}
