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

    public function GetId()
    {
        return isset($_POST["id"]) ? $_POST["id"] : false;
    }
    
    public function GetTitle()
    {
        return "Gaming merchandise";
    }
    
    public function GetContent($merchandise)
    {

        return "";
    }
}