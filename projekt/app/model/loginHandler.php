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
        //
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
                $_SESSION["steamOpenId"] = $this->lightopenid->identity;
                $_SESSION["steamId"] = str_replace("http://steamcommunity.com/openid/", "", $_SESSION["steamOpenId"]);
                
                die($_SESSION["steamId"]);
            }
        }
    }
    
}