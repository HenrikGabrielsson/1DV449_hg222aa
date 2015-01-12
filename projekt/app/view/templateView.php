<?php 

namespace view;

class TemplateView
{

    //Skriver ut det inehåll som ska visas på alla sidor.
    public function EchoContent($title, $content, $loggedIn)
    {
        //sätter inget manifest-attribut vid utloggning eller inloggning.
        $manifest = isset($_GET["login"]) || isset($_GET["logout"]) ? '' : ' manifest="cache.manifest"' ;

        $loginBox = $loggedIn ? $this->GetLoginArea($loggedIn) : "";

        echo '
        <!doctype html>
        <html'.$manifest.'>
            <head>
                <title>'.$title.' - SteamStuff</title>

                <link rel="icon" type="img/ico" href="view/img/favicon.ico">
                <link href="view/style/steamStuffStyle.css" rel="stylesheet" />
            </head>
            
            <body>

                <div id="container">
                    <div id="header">

                        <div id="logo">
                            <img src="view/img/logo.png" id="logo">
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
    private function GetLoginArea($id)
    {
        return 
        '
            <div id="loginBox">
                <img src="model/avatars/'.$id.'.jpg" class="avatar">
                <p>You are logged in! <a href="?logout" class="logoutLink">Log out</a></p>
            </div>
        ';
    }
    
}

