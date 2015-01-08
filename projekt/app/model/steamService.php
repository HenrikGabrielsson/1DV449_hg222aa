<?php

namespace model;

require_once("./configurations.php");
require_once("game.php");
require_once("steamUser.php");
require_once("repository/steamRepository.php");

class SteamService
{
    private $steamRepo;
    
    //url:er till Steam Web API
    private $getPlayerSummariesURL = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/";
    private $getFriendListURL = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?relationship=friend";
    private $getOwnedGames = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?include_appinfo=1&include_played_free_games=0";

    public function __construct()
    {
        $this->steamRepo = new \model\repository\SteamRepository();
    }

    //hämta en användare med givet steamId. Om inget anges hämtas inloggad användare.
    public function GetUser($steamId = null)
    {

        if(!isset($steamId))
        {
            $steamId = $_SESSION["steamId"];
        }
        
        //hämtar eventuellt cachad användare.
        $user = $this->steamRepo->GetUserBySteamId($steamId);

        //kollar här om användaren inte har uppdaterats på länge från Steam.
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
            //den här ska inte återställas och sparas
            $lastFriendListUpdate;

            $user = $this->GetUserFromSteam($steamId,$oldId);
            $games = $this->GetGamesFromSteam($steamId);
            $user->SetGames($games);

            //uppdatera befintlig användare
            if($isOld)
            {
                $lastFriendListUpdate = $user->GetLastFriendListUpdate();
                $this->steamRepo->UpdateUser($user, $lastFriendListUpdate);
            }

            //skapa ny user i db.
            else
            {
                $this->steamRepo->AddUser($user);
            }

            if(isset($lastFriendListUpdate))
            {
                $user->SetLastFriendListUpdate($lastFriendListUpdate);
            }    
        }
        return $user;
    }

    //Hämta ett spel från databasen.
    public function GetCachedGame($id)
    {
        return $this->steamRepo->GetGame($id);
    }

    //token som skydd mot CSRF
    public function SetSecurityToken($token)
    {
        $_SESSION["token"] = $token;
    }

    public function CheckSecurityToken($token)
    {
        if(isset($token) && isset($_SESSION["token"]))
        {
            return $_SESSION["token"] == $token;
        }
        return false;
    }

    //hämta en spelares vänner.
    public function GetFriends($user)
    {

        //först en koll om vänlistan har uppdaterats på länge.
        $lastUpdate = $user->GetLastFriendListUpdate();
        $isOld = false;
        if(isset($lastUpdate))
        {
            $lastUpdateDatetime = new \Datetime($user->GetLastFriendListUpdate());
            $isOld = $lastUpdateDatetime->add(new \DateInterval('P3D')) < new \Datetime(); //3 dagar
        }
        
        //Uppdaterar bara friendlist om den aldrig har skapats eller om den inte uppdaterats på länge.
        if(!isset($lastUpdate) || $isOld)
        {
            $this->UpdateFriendList($user);
        }

        return $this->steamRepo->GetFriendsOf($user);
    }

    //uppdatera en vänlista för en given användare.
    private function UpdateFriendList($user)
    {
        //hämtar vänner från steam. Försöker högst 10 gånger.
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

        // lägg till nya vänskap och ta bort gamla som blivit borttagna
        $this->steamRepo->UpdateFriendships($user, $friends); 
    }

    //hämtar en användare från steam och cachar.
    private function GetUserFromSteam($steamId, $userId)
    {
        //hämtar användaren från steam. Försöker högst 10 gånger.
        for($i = 0; $i < 10; $i++)
        {        
        $json = json_decode(file_get_contents($this->getPlayerSummariesURL . "?key=".\Configurations::$STEAM_API_KEY."&steamids=".$steamId), true);
            
            if(isset($json))
            {
                break;
            }
        }

        $json_player = $json['response']['players'][0];
    
        //spara avataren som en fil.
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

    //sparar avataren i korrekt mapp.
    private function SaveAvatarLocally($remoteURL, $steamId)
    {
        $local = "model/avatars/" . $steamId . ".jpg";

        file_put_contents($local, file_get_contents($remoteURL));

        return $local;
    }
    
    //hämtar alla spel som en spelare äger från Steam.
    private function GetGamesFromSteam($steamId)
    {
        //hämtar spel från steam. Försöker högst 10 gånger.
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