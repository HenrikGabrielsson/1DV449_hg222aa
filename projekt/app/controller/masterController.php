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
    
    //hämtar den sida som användaren vill komma åt.
    public function GetPage()
    {
        $controller;
        
        //kollar så man är inloggad. annars tvingas man göra det.
        if($this->loginHandler->GetLoginId($this->masterView->GetIp(), $this->masterView->GetUserAgent()) && !$this->masterView->UserWantsToLogout())
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
            //utloggning.
            if($this->masterView->UserWantsToLogout())
            {
                $this->loginHandler->Logout();
            }
            
            $controller = new AuthenticationController($this->loginHandler);
        }
        
        $this->templateView->EchoContent($controller->GetTitle(), $controller->GetContent(), $this->loginHandler->GetLoginId());
        
    }

}