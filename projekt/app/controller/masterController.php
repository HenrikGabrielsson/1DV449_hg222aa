<?php

namespace controller;

require_once("./view/templateView.php");
require_once("./controller/authenticationController.php");
require_once("./model/loginHandler.php");

class MasterController
{
    private $loginHandler;
    private $templateView;
    
    public function __construct()
    {
        $this->templateView = new \view\TemplateView();
        $this->loginHandler = new \model\LoginHandler();
        
    }   
    
    public function GetPage()
    {
        $controller;
        
        if($this->loginHandler->GetLoginId())
        {
            die("anything is possible when you're logged in!");
        }
        else
        {
            $controller = new \controller\AuthenticationController($this->loginHandler);
        }
        
        $this->templateView->EchoContent($controller->GetTitle(), $controller->GetContent());
        
    }

}