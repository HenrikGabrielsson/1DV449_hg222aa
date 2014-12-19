<?php
namespace controller;

require_once("./view/templateView.php");
require_once("./view/handler/sessionHandler.php");

class MainController
{
    
    private $templateView;
    private $sessionHandler;
    
    public function __construct()
    {
        $this->templateView = new \view\TemplateView();
        $this->$sessionHandler = new \view\handler\SessionHandler();
    }
    
    public function getPage()
    {
        $this->templateView->echoPage("Welcome", "in a jar");
    }
}