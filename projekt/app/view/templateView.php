<?php

namespace view;

class TemplateView
{
    public function __construct()
    {
        
    }
    
    public function echoPage($title, $content)
    {
        echo 
        '
            <!doctype html>
            <html>
                <head>
                    <title>'.$title.' - Steam Stuff</title>
                </head>
                <body>
                    '.$content.'       
                </body>
            </html>
        ';
    }
}