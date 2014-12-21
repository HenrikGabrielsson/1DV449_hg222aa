<?php 

namespace model;

class SteamUser
{
    private $id;
    private $steamId;
    private $userName;
    private $lastUpdate;
    private $avatar;
    private $games;
    
    public function __construct($id, $steamId, $userName, $lastUpdate, $avatarUrl, $games)
    {
        $this->id = $id;
        $this->steamId = $steamId;
        $this->userName = $userName;
        $this->lastUpdate = $lastUpdate; 
        $this->avatar = $avatarUrl;
        $this->games = $games;
    }
    
    public function GetSteamId()
    {
        return $this->steamId;
    }
    public function GetUserName()
    {
        return $this->userName;
    }
    public function GetAvatar()
    {
        return $this->avatar;
    }
    public function GetGames()
    {
        return $this->games;
    }
}