<?php

namespace controller;

require_once("./view/authenticationView.php");
require_once("IContentController.php");

class AuthenticationController implements IContentController
{
    private $view;
    private $loginHandler;
    
    public function __construct($loginHandler)
    {
        $this->loginHandler = $loginHandler;
        $this->view = new \view\AuthenticationView();  
    }
    
    //hämtar innehåll.
    public function GetContent()
    {
        if($this->view->UserWantsToLogin())
        {
            $this->loginHandler->LoginUser();
        }
        
        return $this->view->GetContent();
    }
    
    //hämtar title
    public function GetTitle()
    {
        return $this->view->GetTitle();
    }
    
    
}