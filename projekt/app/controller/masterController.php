<?php

namespace controller;

require_once("./view/masterView.php");
require_once("./view/templateView.php");

require_once("./controller/authenticationController.php");
require_once("./controller/homePageController.php");
require_once("./controller/suggestController.php");

require_once("./model/loginHandler.php");
require_once("./model/steamService.php");
require_once("./model/ebayService.php");

class MasterController
{
    private $loginHandler;
    private $steamService;
    private $ebayService;
    
    private $templateView;
    private $masterView;
    
    
    public function __construct()
    {
        $this->masterView = new \view\MasterView();
        $this->templateView = new \view\TemplateView();
        
        $this->loginHandler = new \model\LoginHandler();
        $this->steamService = new \model\SteamService();
        $this->ebayService = new \model\EbayService();
        
    }   
    
    public function GetPage()
    {
        $controller;
        
        if($this->loginHandler->GetLoginId())
        {
            switch ($this->masterView->getPath())
            {
                case "suggestions":
                    $controller = new SuggestController($this->steamService,$this->ebayService);
                    break;
                default: 
                    $controller = new HomePageController($this->steamService);
                    break;
            }
        }
        else
        {
            $controller = new AuthenticationController($this->loginHandler);
        }
        
        $this->templateView->EchoContent($controller->GetTitle(), $controller->GetContent());
        
    }

}