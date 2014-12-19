<?php 

namespace view\handler;

class SessionHandler
{
    public function __construct()
    {
        session_start();
    }
    
    public function getSteamId()
    {
        return $_SESSION['steamID'];
    }
    
    public function isLoggedIn()
    {
        return isset($this->getSteamId());
    }
    
    
    
}