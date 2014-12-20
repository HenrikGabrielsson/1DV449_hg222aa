<?php

require_once("../model/loginHandler.php");

namespace controller;

class MasterController
{
    private $loginHandler;
    
    public function __construct()
    {
        $this->$loginHandler = new \model\LoginHandler();
    }   
    
    public function GetPage()
    {
        die("test");
        if(!$this->LoginHandler->IsLoggedIn())
        {
            echo "not logged in";
        }
        else
        {
            echo "logged in";
        }
        
    }

}