<?php 

namespace view;

class SuggestView
{
    private $user;
    
    public function __construct($user)
    {
        $this->user = $user;
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