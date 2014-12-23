<?php

namespace model\repository;

require_once("baseRepository.php");

class SteamRepository extends BaseRepository
{
    public function GetUserBySteamId($steamId)
    {
        $sql = "SELECT * FROM `".$this->userTable."` WHERE steamId = ?"; 
        $params = array($steamId);

        $this->connect();
        
        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        $result = $query->fetch();
        
        $user;
        if($result)
        {
            $games = $this->GetGamesOwnedByUser($result['id']);

            $user = new \model\SteamUser
            (
                $result['id'],
                $result['steamId'],
                $result['userName'],
                $result['lastUpdate'],
                $result['avatar'],
                $games
            );
            return $user;
        }
        return false;
    }

    public function GetGamesOwnedByUser($id)
    {
        $sql = "SELECT * FROM `".$this->gameOwnershipTable."` 
            INNER JOIN `".$this->gameTable."` ON `".$this->gameOwnershipTable."`.gameId = `".$this->gameTable."`.id 
            WHERE `".$this->gameOwnershipTable."`.userId = ?";
        $params = array($id);

        $this->connect();

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        $result = $query->fetchAll();

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
                    $resultGame['recentPlaytime']
                );
            }

            return $games;
        }
        return null;
    }

    public function AddUser($user)
    {
        $sql = "INSERT INTO `".$this->userTable."`(`steamId`, `userName`, `lastUpdate`, `avatar`) VALUES(?,?,?,?);";
        $params = array($user->GetSteamId(), $user->GetUserName(), $user->GetLastUpdate(), $user->GetAvatar());

        $this->connect();

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);



        $this->UpdateOrAddGames($user->GetGames(), $this->dbConnection->lastInsertId());
    }

    public function UpdateOrAddGames($games, $userId)
    {
        $checkSql = "SELECT id FROM `".$this->gameTable."` WHERE appId = ?";
        $gameSql = "INSERT INTO `".$this->gameTable."`(`title`,`appId`) VALUE(?,?);";
        $addGameOwnershipSql = "INSERT INTO `".$this->gameOwnershipTable."`(`userId`,`gameId`,`recentPlaytime`,`overallPlaytime`) VALUES(?,?,?,?)";
        $updateGamerOwnershipSql = "UPDATE `".$this->gameOwnershipTable."` SET `recentPlaytime`=?,`overallPlaytime`=? WHERE userId=? AND gameId=?";

        $checkParams;
        $gameParams;
        $addGameOwnershipParams;
        $updateGamerOwnershipParams;

        $this->connect();

        foreach ($games as $game) 
        {
            $checkParams = array($game->GetAppId());

            $query = $this->dbConnection->prepare($checkSql);
            $query->execute($checkParams);

            $result = $query->fetch();

            if($result)
            {
                $updateGamerOwnershipParams = array($game->GetRecentPlaytime(), $game->GetOverallPlaytime(), $userId, $game->GetId());
                
                $query = $this->dbConnection->prepare($updateGamerOwnershipSql);
                $query->execute($updateGamerOwnershipParams);
            }

            else
            {
                $gameParams = array($game->GetTitle(), $game->GetAppId());
                
                $query = $this->dbConnection->prepare($gameSql);
                $query->execute($gameParams); 


                $addGameOwnershipParams = array($userId, $this->dbConnection->lastInsertId(), $game->GetRecentPlaytime(), $game->GetOverallPlaytime());
                
                $query = $this->dbConnection->prepare($addGameOwnershipSql);
                $query->execute($addGameOwnershipParams);                
            }
        }
    }

    public function UpdateUser($user)
    {
        $sql = "UPDATE `".$this->userTable."` SET `steamId`=?, `userName`=?, `lastUpdate`=?, `avatar`=? WHERE id = ?;";
        $params = array($user->GetSteamId(), $user->GetUserName(), $user->GetLastUpdate(), $user->GetAvatar(), $user->GetId());

        $this->connect();

        $query = $this->dbConnection->prepare($sql);
        $query->execute($params);

        $this->UpdateOrAddGames($user->GetGames(), $user->GetId());
    }
}