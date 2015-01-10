<?php

namespace controller;

require_once("./view/suggestView.php");

require_once("./controller/IContentController.php");

class SuggestController implements IContentController
{
    private $steamService;
    private $ebayService;

    private $suggestView;

    private $suggestionsUser;
    
    public function __construct($steamService, $ebayService)
    {
        $this->steamService = $steamService;
        $this->ebayService = $ebayService;

        $this->suggestView = new \view\SuggestView($this->ebayService, $this->steamService);

        $id = $this->suggestView->GetId();

        if($id)
        {
            $this->suggestionsUser = $this->steamService->GetUser($id);
        }
        else
        {
            $this->suggestionsUser = null;
        }      
    }

    //hämtar sidans title
    public function GetTitle()
    {
        return $this->suggestView->GetTitle($this->suggestionsUser);
    }
    
    //hämtar sidans innehåll.
    public function GetContent()
    {
        //skapar en token som ska jämföras med en som sparas i sessionen för att skydda mot CSRF
        $token = md5(uniqid());
        $this->steamService->SetSecurityToken($token);

        if(isset($this->suggestionsUser))
        {
            return $this->suggestView->GetContent($token, $this->suggestionsUser);            
        }

        return $this->suggestView->GetErrorContent($token);
    }
}