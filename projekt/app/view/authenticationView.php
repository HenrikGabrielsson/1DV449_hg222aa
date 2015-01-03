<?php 

namespace view;

class AuthenticationView
{
    public function GetTitle()
    {
        return "Identify Yourself";
    }
    
    public function GetContent()
    {
        return '
        <p>Login via Steam to get access to the site</p>
        <a href="?login">
            <image src="img/steamlogin.png" alt="Click here to login with your Steam account." />
        </a>
        ';
    }

    //kollar om användaren vill logga in genom steam
    public function UserWantsToLogin()
    {
        if(isset($_GET['login']))
        {
            return true;
        }
    }
}