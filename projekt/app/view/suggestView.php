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
            if(count($this->friends) > 0)
                {
                foreach ($this->friends as $friend) 
                {
                    if($friend->GetSteamId() == $id)
                    {
                        $isFriend = true;
                    }
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
    public function GetContent($token, $suggestionsUser)
    {
        return 
            $this->GetForms($token).   
            '
            <div id="content_header">
                <h1>Merchandise for '.$suggestionsUser->GetUserName().'</h1>
                <img src="model/avatars/'.$suggestionsUser->GetSteamId().'.jpg" class="avatar">
            </div>
            <div id="suggestions">

            </div>
            ';
    }

    public function GetErrorContent($token)
    {
        return
            $this->GetForms($token).
            '<p>Something went wrong and we couldn\'t help you with your request. Try again.</p>
        ';
    }

    private function GetForms($token)
    {
        return 
        '

            <div id="suggestions_options">
                <div id="formsMiniVersion">
                    <form id="forMeForm" method="get" action="?path=suggestions">
                        <input type="hidden" name="path" value="suggestions" />
                        <input type="hidden" name="id" value="'.$this->user->GetSteamId().'">
                        <input type="submit" value="" id="forMeSubmit">
                    </form>

                    <form id="forFriendForm" method="get" action="?path=suggestions">
                        <input type="hidden" name="path" value="suggestions" />
                        <select id="forFriendSelect" name="id">
                            <option value="0" selected>Friend</option>
                            '.$this->GetFriendsOptions().'
                        </select>
                    </form>

                    <p id="token">'.$token.'</p>
                </div>
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

        if(count($this->friends) > 0)
        {
            foreach ($this->friends as $friend) 
            {
                $optionsList .= 
                '<option value="'.$friend->GetSteamId().'"> '.
                $friend->GetUserName().
                '</option>';
            }            
        }

        return $optionsList;
    }
}