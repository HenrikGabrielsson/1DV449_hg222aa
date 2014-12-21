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
    
    public function GetLoginId()
    {
        if(isset($_SESSION["steamId"]))
        {
            return $_SESSION["steamId"];
        }
        return false;
    }
    
    public function LoginUser()
    {
        if(!$this->lightopenid->mode)
        {
            $this->lightopenid->identity = "http://steamcommunity.com/openid/";
            header("Location:" . $this->lightopenid->authUrl());
        }
        
        else if($this->lightopenid->mode == "cancel")
        {
            header("location:" . \Configurations::$DOMAIN);    
        }
        else 
        {
            if($this->lightopenid->validate())
            {
                $this->SetUserSessions();
                header("location: .");
            }
        }
    }
    
    private function SetUserSessions()
    {
        $_SESSION["steamOpenId"] = $this->lightopenid->identity;
        $_SESSION["steamId"] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION["steamOpenId"]);
    }
    
    public function Logout()
    {
    	session_unset(); 
    	session_destroy();         
    }
    
}