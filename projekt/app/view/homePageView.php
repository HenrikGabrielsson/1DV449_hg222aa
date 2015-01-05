<?php

namespace view;

class HomePageView
{
    private $user;
    private $friends;
    
    public function __construct($user, $friends)
    {
        $this->user = $user;   
        $this->friends = $friends; 
    }
    
    //hämtar sidans title.
    public function GetTitle()
    {
        return "Welcome, " . $this->user->GetUserName();
    }
    
    //hämtar sidans innehåll.
    public function GetContent()
    {
        $content = '
        <h1>Hello, ' . $this->user->GetUserName() .'!</h1>
        <p>What do you wish to do today?</p>
        
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

        ';
        
        return $content;
    }

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