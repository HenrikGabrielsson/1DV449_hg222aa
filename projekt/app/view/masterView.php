<?php 

namespace view;

require_once("./configurations.php");

class MasterView
{
    public function GetPath()
    {
        if(isset($_GET["path"]))
        {
            return $_GET["path"];
        }
        return "";
    }

    public function UserWantsToLogout()
    {
    	return isset($_GET["logout"]);
    } 
}