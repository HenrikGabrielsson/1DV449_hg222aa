<?php 

namespace model;

class LoginHandler
{
    public function __construct()
    {
        session_start();
    }
    
    public function GetSteamId()
    {
        return $_SESSION["steamId"];
    }
    
    public function IsLoggedIn()
    {
        return isset(GetSteamId());
    }
}