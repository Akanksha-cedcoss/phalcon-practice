<?php

use Phalcon\Mvc\Controller;


class TryController extends Controller
{
    public function indexAction()
    {
        echo $this->di->get('config')->app->name;
    }
}