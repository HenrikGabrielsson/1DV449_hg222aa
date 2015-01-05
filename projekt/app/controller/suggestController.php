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

        $this->suggestionsUser = $this->steamService->GetUser($this->suggestView->GetId());
    }

    //hämtar sidans title
    public function GetTitle()
    {
        return $this->suggestView->GetTitle($this->suggestionsUser);
    }
    
    //hämtar sidans innehåll.
    public function GetContent()
    {
        $merchandise = $this->ebayService->GetProducts($this->suggestionsUser->GetGames());

        return $this->suggestView->GetContent($merchandise, $this->suggestionsUser);
    }
}