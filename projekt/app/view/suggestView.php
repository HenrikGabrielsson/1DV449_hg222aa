<?php 

namespace view;

class SuggestView
{
    private $user;
    private $friends;
    private $ebayService;
    private $steamService;

    public function __construct($ebayService, $steamService)
    {
        $this->ebayService = $ebayService;
        $this->steamService = $steamService;

        $this->user = $this->steamService->GetUser();
        $this->friends = $this->steamService->GetFriends($this->user);
    }

    public function GetId()
    {
        return isset($_GET["id"]) ? $_GET["id"] : false;
    }
    
    public function GetTitle()
    {
        return "Gaming merchandise";
    }
    
    public function GetContent($merchandise, $suggestionsUser)
    {
        return 
        "
            <div id='suggestions'>
            </div>
        ";
    }

    public function GetMerchandise($id)
    {
        $suggestionsUser = $this->steamService->GetUser($id);
        $merchandise = $this->ebayService->GetProducts($suggestionsUser->GetGames());

        $suggestionList = $this->GetSuggestionList($merchandise, $suggestionsUser->GetGames());

        return $suggestionList;
    }

    private function GetSuggestionList($merchandise, $games)
    {
        $suggestionList = "<ul>";

        foreach ($merchandise as $item) 
        {
            $thisGame;
            foreach ($games as $game) 
            {
                if($game->GetId() == $item->GetGameId())
                {
                    $thisGame = $game;
                    break;
                }
            }

            $suggestionList .= $this->GetListItem($item, $thisGame);
            
        }

        return $suggestionList . "</ul>";
    }

    private function GetListItem($item, $game)
    {

        $startTime = $item->GetStartTime();
        $endTime = $item->GetEndTime();

        return 
        '
        <li>
        <div class="itemDisplay">
            <p>game: '.$game->GetTitle().'</p>
            <img src="'.$item->GetImageURL().'" />
            <dl>
                <dt>Location:</dt>
                <dd>'.$item->GetLocation().'</dd>

                <dt>Country:</dt>
                <dd>'.$item->GetCountry().'</dd>

                <dt>Auction started at:</dt>
                <dd>'.$startTime->format('Y-m-d H:i:s').'</dd>

                <dt>Auction ends at: </dt>
                <dd>'.$endTime->format('Y-m-d H:i:s').'</dd>
            </dl>

            <p><a href="'.$item->GetEbayURL().'">'.$item->GetTitle().'</a></p>

        </div>
        </li>
        ';
    }
}