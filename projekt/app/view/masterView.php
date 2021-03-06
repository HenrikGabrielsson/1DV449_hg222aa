<?php 

namespace view;

require_once("./configurations.php");

class MasterView
{
    //kollar vilken sida användaren vill komma åt.
    public function GetPath()
    {
        if(isset($_GET["path"]))
        {
            return $_GET["path"];
        }
        return "";
    }

    //kollar om användaren vill logga ut.
    public function UserWantsToLogout()
    {
    	return isset($_GET["logout"]);
    } 

    public function GetUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function GetIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }
}