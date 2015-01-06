<?php 

namespace view;

class TemplateView
{

    //Skriver ut det inehåll som ska visas på alla sidor.
    public function EchoContent($title, $content, $loggedIn)
    {
        $loginBox = $loggedIn ? $this->GetLogoutBox() : "";

        echo '
        <!doctype html>
        <html>
            <head>
                <title>'.$title.' - SteamStuff</title>

                <link rel="icon" type="img/ico" href="view/img/favicon.ico">
                <link href="view/style/steamStuffStyle.css" rel="stylesheet" />
            </head>
            
            <body>

                <div id="container">
                    <div id="header">

                        <div id="logo">
                            <img src="view/img/logo.png">
                        </div>

                        '. $loginBox .'
                    </div>
                    
                    <div id="main_content">
                        '.$content.'
                    </div>
                </div>

                <script type="text/javascript" src="view/script/jquery-1.11.2.min.js"></script>
                <script type="text/javascript" src="view/script/main.js"></script>
            </body>
        </html>
        ';
    }

    //skapar en ruta för utloggning om det ska visas.
    private function GetLogoutBox()
    {
        return 
        '
            <div id="loginBox">
                <p>You are logged in!</p>
                <p><a href="?logout">Log out</a></p>
            </div>
        ';
    }
    
}

