<?php 

namespace view;

class SuggestView
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
        return "Suggestions for " . $this->user->GetUserName();
    }
    
    public function GetContent()
    {
        return "Stuff goes here";
    }
}