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

    //hämtar id på den användare som man vill ha mechandise-förslag till.
    public function GetId()
    {
        return isset($_GET["id"]) ? $_GET["id"] : false;
    }
    
    //Hämta sidans title
    public function GetTitle($suggestionsUser)
    {
        return "Merchandise for " . $suggestionsUser->GetUserName();
    }

    //hämta sidans innehåll.
    public function GetContent($merchandise, $suggestionsUser)
    {
        return 
        '
        <form id="forMeForm" method="get" action="?path=suggestions">
            <input type="hidden" name="path" value="suggestions" />
            <input type="hidden" name="id" value="'.$this->user->GetSteamId().'">
            <input type="submit" value="" id="forMeSubmit">
        </form>

        <form id="forFriendForm" method="get" action="?path=suggestions">
            <input type="hidden" name="path" value="suggestions" />
            <select id="forFriendSelect" name="id">
                <option value="0" selected>Choose Friend</option>
                '.$this->GetFriendsOptions().'
            </select>

        </form>

        <div id="suggestions">
        </div>
        ';
    }

    //hämta merchandise.
    public function GetMerchandise($id)
    {
        $suggestionsUser = $this->steamService->GetUser($id);
        $merchandise = $this->ebayService->GetProducts($suggestionsUser->GetGames());

        return $merchandise;
    }

    //hämta vänner i en select-lista.
    private function GetFriendsOptions()
    {
        $optionsList = "";

        foreach ($this->friends as $friend) 
        {
            $optionsList .= 
            '<option value="'.$friend->GetSteamId().'"> '.
            $friend->GetUserName().
            '</option>';
        }

        return $optionsList;
    }
}