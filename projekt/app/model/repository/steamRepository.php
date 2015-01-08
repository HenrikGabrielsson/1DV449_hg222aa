<?php

namespace model\repository;

require_once("baseRepository.php");

class SteamRepository extends BaseRepository
{

    //Hämta användare efter givet SteamId
    public function GetUserBySteamId($steamId)
    {
        $sql = "SELECT * FROM `".$this->userTable."` WHERE steamId = ?"; 
        $params = array($steamId);

        $result = $this->RunQuery($sql, $params);
        $result = $result[0];
        
        //om det finns en användare i databasen så skapas den och returneras. Annars false.
        if($result)
        {
            $games = $this->GetGamesOwnedByUser($result['id']);

            $user = new \model\SteamUser
            (
                $result['id'],
                $result['steamId'],
                $result['userName'],
                $result['lastUpdate'],
                $result['lastFriendListUpdate'],
                $result['avatar'],
                $games
            );
            return $user;
        }
        return false;
    }

    //Hämta användare efter givet id
    public function GetUserById($id)
    {
        $sql = "SELECT * FROM `".$this->userTable."` WHERE id = ?"; 
        $params = array($id);

        $result = $this->RunQuery($sql, $params);
        $result = $result[0];

        //om det finns en användare i databasen så skapas den och returneras. Annars false.
        if($result)
        {
            $games = $this->GetGamesOwnedByUser($result['id']);

            $user = new \model\SteamUser
            (
                $result['id'],
                $result['steamId'],
                $result['userName'],
                $result['lastUpdate'],
                $result['lastFriendListUpdate'],
                $result['avatar'],
                $games
            );
            return $user;
        }
        return false;
    }

    //hämtar alla spel som ägs av given användare
    public function GetGamesOwnedByUser($id)
    {
        $sql = "SELECT * FROM `".$this->gameOwnershipTable."` 
            INNER JOIN `".$this->gameTable."` ON `".$this->gameOwnershipTable."`.gameId = `".$this->gameTable."`.id 
            WHERE `".$this->gameOwnershipTable."`.userId = ?";
        $params = array($id);

        $result = $this->RunQuery($sql, $params);

        // om det finns några spel att hämta så skapas de och returneras. annars null.
        if($result)
        {
            $games = array();

            foreach ($result as $resultGame) 
            {
                $games[] = new \model\Game
                (
                    $resultGame['gameId'],
                    $resultGame['appId'],
                    $resultGame['title'],
                    $resultGame['overallPlaytime'],
                    $resultGame['recentPlaytime'],
                    $resultGame['lastMerchandiseUpdate']
                );
            }

            return $games;
        }
        return null;
    }

    //lägger till en användare i databasen.
    public function AddUser($user)
    {
        $sql = "INSERT INTO `".$this->userTable."`(`steamId`, `userName`, `lastUpdate`, `avatar`) VALUES(?,?,?,?);";
        $params = array($user->GetSteamId(), $user->GetUserName(), $user->GetLastUpdate(), $user->GetAvatar());

        $this->RunQuery($sql, $params);

        $this->UpdateOrAddGames($user->GetGames(), $this->dbConnection->lastInsertId());
    }

    //Lägger till en användare som ägare av ett spel.
    public function UpdateOrAddGames($games, $userId)
    {
        $checkSql = "SELECT id FROM `".$this->gameTable."` WHERE appId = ?";
        $gameSql = "INSERT INTO `".$this->gameTable."`(`title`,`appId`, `lastMerchandiseUpdate`) VALUE(?,?,?);";
        $addGameOwnershipSql = "INSERT INTO `".$this->gameOwnershipTable."`(`userId`,`gameId`,`recentPlaytime`,`overallPlaytime`) VALUES(?,?,?,?)";
        $updateGamerOwnershipSql = "UPDATE `".$this->gameOwnershipTable."` SET `recentPlaytime`=?,`overallPlaytime`=? WHERE userId=? AND gameId=?";

        foreach ($games as $game) 
        {
            //koll om spelet redan finns cachat.
            $checkParams = array($game->GetAppId());
            $result = $this->RunQuery($checkSql, $checkParams);
            $result = $result[0];

            //finns redan, uppdateras med ägare.
            if($result)
            {
                $updateGamerOwnershipParams = array($game->GetRecentPlaytime(), $game->GetOverallPlaytime(), $userId, $game->GetId());

                $this->RunQuery($updateGamerOwnershipSql, $updateGamerOwnershipParams);
            }

            //finns inte, spelet skapas...Ägare läggs till.
            else
            {
                $gameParams = array($game->GetTitle(), $game->GetAppId(), $game->GetLastMerchandiseUpdate());
                $this->RunQuery($gameSql, $gameParams);

                $addGameOwnershipParams = array($userId, $this->dbConnection->lastInsertId(), $game->GetRecentPlaytime(), $game->GetOverallPlaytime());
                $this->RunQuery($addGameOwnershipSql, $addGameOwnershipParams);          
            }
        }
    }

    //hämta spel efter givet id.
    public function GetGame($id)
    {
        $sql = "SELECT * FROM `".$this->gameTable."` WHERE id=?";
        $params = array($id);

        $result = $this->RunQuery($sql, $params);
        $result = $result[0];

        //returnerar spel om det finns .annars false.
        if($result)
        {
            return new \model\Game
            (
                $result['gameId'],
                $result['appId'],
                $result['title'],
                $result['overallPlaytime'],
                $result['recentPlaytime'],
                $result['lastMerchandiseUpdate']
            );
        }

        return false;
    }

    //uppdatera befintligt spel med nya uppgifter.
    public function UpdateGame($game)
    {
        $date = $game->GetLastMerchandiseUpdate();

        $sql = "UPDATE `".$this->gameTable."` SET `appId`=?, `title`=?, `lastMerchandiseUpdate`=? WHERE `id`=?";
        $params = array($game->GetAppId(), $game->GetTitle(), $date->format('Y-m-d H:i:s'), $game->GetId());

        $this->RunQuery($sql, $params);
    }

    //uppdatera befintlig användare med nya uppgifter.
    public function UpdateUser($user, $lastFriendListUpdate)
    {
        $sql = "UPDATE `".$this->userTable."` SET `steamId`=?, `userName`=?, `lastUpdate`=?, `lastFriendListUpdate`=?, `avatar`=? WHERE id = ?;";
        $params = array($user->GetSteamId(), $user->GetUserName(), $user->GetLastUpdate(), $lastFriendListUpdate, $user->GetAvatar(), $user->GetId());

        $this->RunQuery($sql, $params);

        $this->UpdateOrAddGames($user->GetGames(), $user->GetId());
    }

    //uppdatera vänskapsband.
    public function UpdateFriendships($user, $steamFriends)
    {

        //redan cachade vänner till $user
        $cachedFriends = $this->GetFriendsOf($user);

        //inga vänner. behöver ej köra funktionen
        if(count($cachedFriends) == 0 || count($steamFriends) == 0)
        {
            return;
        }

        //tar bort vänskapsband som inte längre finns.
        foreach($cachedFriends as $friend)
        {
            if(!in_array($friend, $steamFriends))
            {
                $this->DeleteFriendship($user, $friend);
            }
        }

        //lägger till vänskapsband som inte redan finns.
        foreach($steamFriends as $friend)
        {
            if(!in_array($friend, $cachedFriends))
            {
                $this->AddFriendship($user, $friend);
            }            
        } 

        $sql = "UPDATE `".$this->userTable."` SET `lastFriendListUpdate`=? WHERE `id`=?;";
        $params = array(date("Y-m-d H:i:s"), $user->GetId());

        $this->RunQuery($sql, $params);
    }

    //lägger till ett vänskapsband
    private function AddFriendship($friend1, $friend2)
    {
        $sql = "INSERT INTO `".$this->friendshipTable."` (`friend1`,`friend2`) VALUES(?,?)";
        $params = array($friend1->GetId(), $friend2->GetId());

        $this->RunQuery($sql, $params);
    }

    //tar bort ett vänskapsband
    private function DeleteFriendship($friend1, $friend2)
    {
        $sql = "DELETE FROM `".$this->friendshipTable."` WHERE (`friend1` = ? AND `friend2` = ?) OR (`friend2` = ? AND `friend1` = ?)";
        $params = array($friend1->GetId(), $friend2->GetId(), $friend1->GetId(), $friend2->GetId());

        $this->RunQuery($sql, $params);
    }

    //Hämtar alla vänner till given användare.
    public function GetFriendsOf($user)
    {
        $sql = "SELECT * FROM `".$this->friendshipTable."` WHERE friend1 = ? OR friend2 = ?";
        $params = array($user->GetId(), $user->GetId());

        $result = $this->RunQuery($sql, $params);

        if($result)
        {
            $friends = array();
            foreach($result as $friendship)
            {
                if($friendship["friend1"] == $user->GetId())
                {
                    $friends[] = $this->GetUserById($friendship["friend2"]);
                }
                else
                {
                    $friends[] = $this->GetUserById($friendship["friend1"]);
                }        
            }
            return $friends;
        }
    }
}