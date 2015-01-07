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
    
    //hämtar title
    public function GetTitle()
    {
        return $this->homePageView->GetTitle();
    }
    
    //hämtar sidinnehåll
    public function GetContent()
    {
        //skapar en token som ska jämföras med en som sparas i sessionen för att skydda mot CSRF
        $token = md5(uniqid());
        $this->steamService->SetSecurityToken($token);

        return $this->homePageView->GetContent($token);
    }
}