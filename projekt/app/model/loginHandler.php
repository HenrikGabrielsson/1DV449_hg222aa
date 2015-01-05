<?php 

namespace model;

require_once("vendor/lightopenid.php");
require_once("./configurations.php");

class LoginHandler
{
    private $lightOpenId;
    
    public function __construct()
    {
        session_start();    
        $this->lightopenid = new \LightOpenID(\Configurations::$DOMAIN);
    }
    
    //Hämtar steamId för inloggad användare om det finns.
    public function GetLoginId()
    {
        return isset($_SESSION["steamId"]) ? $_SESSION["steamId"] : false;
    }
    
    //körs när en användare försöker logga in.
    public function LoginUser()
    {
        //användaren skickas till steam för att logga in och godkänna att SteamStuff får logga in med Steam-kontot.
        if(!$this->lightopenid->mode)
        {
            $this->lightopenid->identity = "http://steamcommunity.com/openid/";
            header("Location:" . $this->lightopenid->authUrl());
        }
        
        //användaren avbryter inloggningen. Skickas till startsidan.
        else if($this->lightopenid->mode == "cancel")
        {
            header("location:" . \Configurations::$DOMAIN);    
        }
        //användaren loggar in.
        else 
        {
            //sessioner sätts om allt går bra.
            if($this->lightopenid->validate())
            {
                $this->SetUserSessions();
                header("location: .");
            }
        }
    }
    
    //Sätter session för inloggning.
    private function SetUserSessions()
    {
        $_SESSION["steamOpenId"] = $this->lightopenid->identity;
        $_SESSION["steamId"] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION["steamOpenId"]);
    }
    
    //förstör sessionen vid utloggning.
    public function Logout()
    {
    	session_unset(); 
    	session_destroy();         
    }
    
}