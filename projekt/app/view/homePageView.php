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
    
    public function GetTitle()
    {
        return "Welcome, " . $this->user->GetUserName();
    }
    
    public function GetContent()
    {
        $content = '
        <h1>Hello, ' . $this->user->GetUserName() .'!</h1>
        <p>What do you wish to do today?</p>
        
        <form id="forMeForm" method="post" action="?path=suggestions">
            <input type="hidden" name="id" value="'.$this->user->GetSteamId().'">
            <input type="submit" value="" id="forMeSubmit">
        </form>

        <form id="forFriendForm" method="post" action="?path=suggestions">
            <select id="forFriendSelect" name="id">
                <option value="0" selected>Choose Friend</option>
                '.$this->GetFriendsOptions().'
            </select>
            <input type="submit" value="Get suggestions for friend" id="forFriendSubmit" />

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