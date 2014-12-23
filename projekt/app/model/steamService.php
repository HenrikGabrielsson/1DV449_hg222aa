<?php

namespace model;

require_once("./configurations.php");
require_once("game.php");
require_once("steamUser.php");
require_once("repository/steamRepository.php");

class SteamService
{
    private $steamRepo;
    
    private $getPlayerSummariesURL = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/";
    private $getFriendListURL = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/";
    private $getOwnedGames = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?include_appinfo=1&include_played_free_games=1";

    public function __construct()
    {
        $this->steamRepo = new \model\repository\SteamRepository();
    }


    public function GetUser($steamId = null)
    {
        if(!isset($steamId))
        {
            $steamId = $_SESSION["steamId"];
        }
        
        $user = $this->steamRepo->GetUserBySteamId($steamId);

        //om användaren inte finns cachad redan så hämtas den från Steam och cachas
        if(!$user)
        {
            $user = $this->GetUserFromSteam($steamId);
            $games = $this->GetGames($steamId);
            $user->SetGames($games);

            $this->steamRepo->AddUser($user);
        }
        return $user;
    }
    
    private function GetUserFromSteam($steamId)
    {
        $json = json_decode(file_get_contents($this->getPlayerSummariesURL . "?key=".\Configurations::$STEAM_API_KEY."&steamids=".$steamId), true);
        $json_player = $json['response']['players'][0];
    
        $avatar = $this->SaveAvatarLocally($json_player['avatarmedium'], $json_player['steamid']);

        return new SteamUser (
            null,
            $json_player['steamid'],
            $json_player['personaname'],
            time(),
            $json_player['avatarmedium'],
            $avatar, 
            null
        );   
    }

    private function SaveAvatarLocally($remoteURL, $steamId)
    {
        $local = "http://henrikgabrielsson.se/SteamStuff/model/avatars/" . $steamId . ".jpg";

        file_put_contents($local, file_get_contents($remoteURL));

        die();
        return $local;
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