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
            </head>
            
            <body>
                '.$content.'
            </body>
        </html>
        ';
    }
    
}

