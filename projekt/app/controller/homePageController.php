<?php 

namespace controller;

require_once("./view/homePageView.php");

require_once("IContentController.php");

class HomePageController implements IContentController
{
    private $homePageView;
    
    private $steamService; 
    private $steamUser;
    
    public function __construct($steamService)
    {
        $this->steamService = $steamService;
        $this->steamUser = $this->steamService->GetUser();
        
        $this->homePageView = new \view\HomePageView($this->steamUser);
    }
    
    public function GetTitle()
    {
        return $this->homePageView->GetTitle();
    }
    
    public function GetContent()
    {
        return $this->homePageView->GetContent();
    }
}