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
    private $getFriendListURL = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?relationship=friend";
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

        $oldId = null;
        $isOld = false;
        if($user)
        {
            $lastUpdate = new \Datetime($user->GetLastUpdate());
            $isOld = $lastUpdate->add(new \DateInterval('P1D')) < new \Datetime();
            $oldId = $user->GetId();
        }


        //om användaren inte finns cachad eller om den inte har uppdaterats på ett dygn redan så hämtas den från Steam och cachas
        if(!$user || $isOld)
        {
            $user = $this->GetUserFromSteam($steamId,$oldId);
            $games = $this->GetGamesFromSteam($steamId);
            $user->SetGames($games);

            //uppdatera befintlig användare
            if($isOld)
            {
                $this->steamRepo->UpdateUser($user);
            }

            //skapa ny user i db.
            else
            {
                $this->steamRepo->AddUser($user);
            }
        }
        return $user;
    }

    public function GetCachedGame($id)
    {
        return $this->steamRepo->GetGame($id);
    }

    public function GetFriends($user)
    {
        $lastUpdate = $user->GetLastFriendListUpdate();

        $isOld = false;
        if(isset($lastUpdate))
        {
            $lastUpdateDatetime = new \Datetime($user->GetLastFriendListUpdate());
            $isOld = $lastUpdateDatetime->add(new \DateInterval('P3D')) < new \Datetime(); //5 dagar
        }
        
        //Uppdaterar bara friendlist om den aldrig har skapats eller om den inte uppdaterats på länge.
        if(!isset($lastUpdate) || $isOld)
        {
            $this->UpdateFriendList($user);
        }

        return $this->steamRepo->GetFriendsOf($user);
    }

    private function UpdateFriendList($user)
    {

        for($i = 0; $i < 10; $i++)
        {
            $json = json_decode(file_get_contents($this->getFriendListURL . "&key=".\Configurations::$STEAM_API_KEY."&steamid=".$user->GetSteamId()),true);
            
            if(isset($json))
            {
                break;
            }
        }
        $json_friends = $json['friendslist']["friends"];   

        $friends = array();

        //hämta vänner från databas eller skapa om de inte finns
        foreach($json_friends as $json_friend)
        {
            $friends[] = $this->GetUser($json_friend["steamid"]);
        }

        $this->steamRepo->UpdateFriendships($user, $friends); // lägg till nya vänskap och ta bort gamla som blivit borttagna :(   
    }

    private function GetUserFromSteam($steamId, $userId)
    {
        for($i = 0; $i < 10; $i++)
        {        
        $json = json_decode(file_get_contents($this->getPlayerSummariesURL . "?key=".\Configurations::$STEAM_API_KEY."&steamids=".$steamId), true);
            
            if(isset($json))
            {
                break;
            }
        }

        $json_player = $json['response']['players'][0];
    
        $avatar = $this->SaveAvatarLocally($json_player['avatarmedium'], $json_player['steamid']);

        return new SteamUser (
            $userId,
            $json_player['steamid'],
            $json_player['personaname'],
            date("Y-m-d H:i:s"),
            null,
            $avatar, 
            null
        );   
    }

    private function SaveAvatarLocally($remoteURL, $steamId)
    {
        $local = "model/avatars/" . $steamId . ".jpg";

        file_put_contents($local, file_get_contents($remoteURL));

        return $local;
    }
    
    private function GetGamesFromSteam($steamId)
    {
        for($i = 0; $i < 10; $i++)
        {
            $json = json_decode(file_get_contents($this->getOwnedGames . "&key=".\Configurations::$STEAM_API_KEY."&steamid=".$steamId), true);
            
            if(isset($json))
            {
                break;
            }
        }        

        $json_games = $json['response']['games']; 
        
        $games = array();
        foreach ($json_games as $game) {
            
            $games[] = new Game (
                null, 
                $game['appid'],$game['name'], 
                $game['playtime_forever'], 
                isset($game['playtime_2weeks']) ? $game['playtime_2weeks'] : 0, //denna parameter följer inte med vid 0 by default
                null
            );
        }
        
        return $games;
    }
}