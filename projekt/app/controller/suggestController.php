<?php

namespace controller;

require_once("./view/suggestView.php");

require_once("./controller/IContentController.php");

class SuggestController implements IContentController
{
    private $steamService;
    private $ebayService;

    private $user;
    private $friends;

    private $suggestView;
    
    public function __construct($steamService, $ebayService)
    {
        $this->steamService = $steamService;
        $this->ebayService = $ebayService;

        $this->user = $this->steamService->GetUser();
        $this->friends = $this->steamService->GetFriends($this->user);

        $this->suggestView = new \view\SuggestView($this->user, $this->friends);
    }
    
    public function GetTitle()
    {
        return $this->suggestView->GetTitle();
    }
    
    public function GetContent()
    {
        $suggestionsUser = $this->steamService->GetUser($this->suggestView->GetId());

        $merchandise = $this->ebayService->GetProducts($suggestionsUser->GetGames());

        return $this->suggestView->GetContent($merchandise, $suggestionsUser);
    }
}