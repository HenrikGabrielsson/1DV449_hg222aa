<?php 

namespace model;

//detta är en steamanvändare och även en användare på SteamStuff
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
        return strip_tags($this->id);
    }
    public function GetSteamId()
    {
        return strip_tags($this->steamId);
    }
    public function GetUserName()
    {
        return strip_tags($this->userName);
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
        return strip_tags($this->avatar);
    }
    public function GetGames()
    {
        return $this->games;
    }
}