<?php

namespace controller;

require_once("./view/authenticationView.php");

class AuthenticationController
{
    private $view;
    private $loginHandler;
    
    public function __construct($loginHandler)
    {
        $this->loginHandler = $loginHandler;
        $this->view = new \view\AuthenticationView();  
    }
    
    public function GetContent()
    {
        if($this->view->UserWantsToLogin())
        {
            $this->loginHandler->LoginUser();
        }
        
        return $this->view->GetContent();
    }
    
    public function GetTitle()
    {
        return $this->view->GetTitle();
    }
    
    
}