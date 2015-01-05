<?php 

namespace controller;

require_once("./view/homePageView.php");

require_once("IContentController.php");

class HomePageController implements IContentController
{
    private $homePageView;
    
    private $steamService; 
    
    public function __construct($steamService)
    {
        $this->steamService = $steamService;

        $user = $this->steamService->GetUser();
        $friends = $this->steamService->GetFriends($user);

        $this->homePageView = new \view\HomePageView($user, $friends);
    }
    
    //hämtar sidinnehåll
    public function GetTitle()
    {
        return $this->homePageView->GetTitle();
    }
    
    //hämtar title
    public function GetContent()
    {
        return $this->homePageView->GetContent();
    }
}