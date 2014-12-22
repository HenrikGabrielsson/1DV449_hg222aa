<?php

namespace model;

require_once("./configurations.php");
require_once("game.php");
require_once("steamUser.php");

class SteamService
{
    private $getPlayerSummariesURL = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/";
    private $getFriendListURL = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/";
    private $getOwnedGames = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?include_appinfo=1&include_played_free_games=1";

    public function GetUser($steamId = null)
    {
        if(!isset($steamId))
        {
            $steamId = $_SESSION["steamId"];
        }
        
        $user = $this->GetUserFromSteam($steamId);
        $games = $this->GetGames($steamId);
        $user->SetGames($games);
        
        return $user;
    }
    
    private function GetUserFromSteam($steamId)
    {
        $json = json_decode(file_get_contents($this->getPlayerSummariesURL . "?key=".\Configurations::$STEAM_API_KEY."&steamids=".$steamId), true);
        $json_player = $json['response']['players'][0];
    
        return new SteamUser (
            null,
            $json_player['steamid'],
            $json_player['personaname'],
            time(),
            $json_player['avatarmedium'],
            null
        );   
    }
    
    private function GetGames($steamId)
    {
        $json = json_decode(file_get_contents($this->getOwnedGames . "&key=".\Configurations::$STEAM_API_KEY."&steamid=".$steamId), true);
        $json_games = $json['response']['games']; 
        
        $games = array();
        foreach ($json_games as $game) {
            
            $games[] = new Game (
                null, 
                $game['appid'],$game['name'], 
                $game['playtime_forever'], 
                isset($game['playtime_2weeks']) ? $game['playtime_2weeks'] : 0
            );
        }
        
        return $games;
    }
}