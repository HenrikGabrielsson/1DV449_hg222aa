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
}