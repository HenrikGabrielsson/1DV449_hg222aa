<?php 

namespace model;

class Game
{
    private $id;
    private $appId;
    private $title;
    private $overallPlaytime;
    private $recentPlaytime;
    
    public function __construct($id, $appId, $title, $overallPlaytime, $recentPlaytime)
    {
        $this->id = $id;
        $this->appId = $appId;
        $this->title = $title;
        $this->overallPlaytime = $overallPlaytime;
        $this->recentPlaytime = $recentPlaytime;
    }
}