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
    public function GetContent($token)
    {
        $content = '
        <div id="text_content">
            <h1 class="introduction_h1">Hello, ' . $this->user->GetUserName() .'!</h1>
            <p class="introduction_p">Who do you wish to find stuff for today? For yourself or for a friend? </p>
        </div>
        <div id="homePageForms">
        <form id="forMeForm" method="get" action="?path=suggestions">
            <input type="hidden" name="path" value="suggestions" />
            <input type="hidden" name="id" value="'.$this->user->GetSteamId().'">
            <p class="forMeFormText"> For me </p>
            <input type="submit" value="" id="forMeSubmit">
        </form>

        <form id="forFriendForm" method="get" action="?path=suggestions">
            <input type="hidden" name="path" value="suggestions" />
            <input type="hidden" name="token" id="token" value="'.$token.'">
            <select id="forFriendSelect" name="id">
                <option value="0" selected>For a friend</option>
                '.$this->GetFriendsOptions().'
            </select>

        </form>
        </div>

        ';
        
        return $content;
    }

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