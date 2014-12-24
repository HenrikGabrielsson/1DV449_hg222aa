<?php

namespace controller;

require_once("./view/suggestView.php");

require_once("./controller/IContentController.php");

class SuggestController implements IContentController
{
    private $steamService;
    private $suggestView;
    
    public function __construct($steamService)
    {
        $this->steamService = $steamService;

        $user = $this->steamService->GetUser();
        $friends = $this->steamService->GetFriends($user);

        $this->suggestView = new \view\SuggestView($user, $friends);
    }
    
    public function GetTitle()
    {
        return $this->suggestView->GetTitle();
    }
    
    public function GetContent()
    {
        return $this->suggestView->GetContent();
    }
}