<?php 

namespace view;

class TemplateView
{

    public function EchoContent($title, $content)
    {
        echo '
        <!doctype html>
        <html>
            <head>
                <title>'.$title.' - SteamStuff</title>

                <link href="view/style/steamStuffStyle.css" rel="stylesheet" />
            </head>
            
            <body>

                <div id="container">
                    <div id="header">
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
    
}

