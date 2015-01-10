<?php 

namespace view;

class AuthenticationView
{

    //hämtar sidans title
    public function GetTitle()
    {
        return "Identify Yourself";
    }
    
    //hämtar sidans innehåll.
    public function GetContent()
    {
        return '

        <div id="text_content">
            <h1>Welcome to SteamStuff</h1>
            <p>SteamStuff helps you find t-shirts, posters, special editions and other gaming merchandise for your favorite games. You don\'t even have to do any searching yourself! All
            you have to do is log in with your Steam account (don\'t worry, it\'s safe!) and we will check what items might be perfect just for you. You can also find stuff to give to your friends. </p>
            <p>Login via Steam to get access to the site</p>
            <a href="?login">
                <image src="view/img/steamlogin.png" alt="Click here to login with your Steam account." />
            </a>
        </div>
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

    public function GetUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public function GetIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }
}