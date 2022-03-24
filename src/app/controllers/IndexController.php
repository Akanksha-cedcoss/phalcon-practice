<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        
    }
    public function dashboardAction()
    {
        if (!($this->di->get('session')->has('id') or $this->cookies->has('remember-me'))) {
            $this->response->redirect("index/index");
        }
        $this->view->name = $this->di->get('session')->name;
    }
}
