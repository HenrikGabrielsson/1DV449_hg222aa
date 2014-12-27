<?php 

namespace model;

class Game
{
    private $id;
    private $appId;
    private $title;
    private $overallPlaytime;
    private $recentPlaytime;
    private $lastMerchandiseUpdate;
    
    public function __construct($id, $appId, $title, $overallPlaytime, $recentPlaytime, $lastMerchandiseUpdate)
    {
        $this->id = $id;
        $this->appId = $appId; //Steams spelid
        $this->title = $title;
        $this->overallPlaytime = $overallPlaytime;
        $this->recentPlaytime = $recentPlaytime;
        $this->lastMerchandiseUpdate = $lastMerchandiseUpdate;
    }

    public function SetLastMerchandiseUpdate($date)
    {
        $this->lastMerchandiseUpdate = $date;
    }

    public function GetId()
    {
        return $this->id;
    }
    public function GetAppId()
    {
        return $this->appId;
    }
    public function GetTitle()
    {
        return $this->title;
    }
    public function GetOverallPlaytime()
    {
        return $this->overallPlaytime;
    }
    public function GetRecentPlaytime()
    {
        return $this->recentPlaytime;
    }
    public function GetLastMerchandiseUpdate()
    {
        return $this->lastMerchandiseUpdate;
    }
}