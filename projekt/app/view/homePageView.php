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
        <p><a href="?path=suggestions&for=me">Look at merchandise for me</a></p>
        <p><a href="?path=suggestions&for=friends">Look at merchandise for a friend</a></p>
        ';
        
        return $content;
    }
}