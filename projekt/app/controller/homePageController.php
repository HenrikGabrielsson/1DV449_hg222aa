<?php 

namespace controller;

require_once("IContentController.php");

class HomePageController implements IContentController
{
    private $steamService; 
    private $steamUser;
    
    public function __construct($steamService)
    {
        $this->steamService = $steamService;
        $this->steamUser = $this->steamService->GetUser();
        
    }
    
    public function GetTitle()
    {
        
    }
    
    public function GetContent()
    {
    }
}