<?php
namespace App\components;

class Helper
{
    private $title ="This is heading from helper";
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title)
    {
        $this->title = $title;
    }
}