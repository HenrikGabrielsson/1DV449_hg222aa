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

        if(isset($_GET["id"]))
        {
            $id = $_GET["id"];

            //kollar så användaren är användaren själv eller en vän. annars är åtkomst inte tillåten.
            $isFriend = false;
            foreach ($this->friends as $friend) 
            {
                if($friend->GetSteamId() == $id)
                {
                    $isFriend = true;
                }
            }

            if($this->user->GetSteamId() == $id || $isFriend)
            {
                return $id;
            }

        }
        return false;
    }
    
    //Hämta sidans title
    public function GetTitle($suggestionsUser)
    {
        return isset($suggestionsUser) ? "Merchandise for " . $suggestionsUser->GetUserName() : "Error";
    }

    //hämta sidans innehåll.
    public function GetContent($merchandise, $suggestionsUser, $token)
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
            <input type="hidden" name="token" id="token" value="'.$token.'">
            <select id="forFriendSelect" name="id">
                <option value="0" selected>Choose Friend</option>
                '.$this->GetFriendsOptions().'
            </select>

        </form>

        <div id="suggestions">
        </div>
        ';
    }

    public function GetErrorContent($token)
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
                <input type="hidden" name="token" id="token" value="'.$token.'">
                <select id="forFriendSelect" name="id">
                    <option value="0" selected>Choose Friend</option>
                    '.$this->GetFriendsOptions().'
                </select>

            </form>

            <p>Something went wrong and we couldn\'t help you with your request. Try again.</p>
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