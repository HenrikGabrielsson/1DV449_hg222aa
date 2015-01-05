<?php 

namespace model;

class SteamUser
{
    private $id;
    private $steamId;
    private $userName;
    private $lastUpdate;
    private $lastFriendListUpdate;
    private $avatar;
    private $games;
    
    public function __construct($id, $steamId, $userName, $lastUpdate, $lastFriendListUpdate, $avatarUrl, $games)
    {
        $this->id = $id;
        $this->steamId = $steamId;
        $this->userName = $userName;
        $this->lastUpdate = $lastUpdate; 
        $this->lastFriendListUpdate = $lastFriendListUpdate;
        $this->avatar = $avatarUrl;
        $this->games = $games;
    }
    
    public function SetGames($games)
    {
        $this->games = $games;
    }

    public function SetLastFriendListUpdate($lastUpdate)
    {
        $this->lastFriendListUpdate = $lastUpdate;
    }

    public function GetId()
    {
        return $this->id;
    }
    public function GetSteamId()
    {
        return $this->steamId;
    }
    public function GetUserName()
    {
        return $this->userName;
    }
    public function GetLastUpdate()
    {
        return $this->lastUpdate;
    }
    public function GetLastFriendListUpdate()
    {
        return $this->lastFriendListUpdate;
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