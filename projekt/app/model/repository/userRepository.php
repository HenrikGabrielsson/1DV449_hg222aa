<?php

namespace model\repository;

require_once("baseRepository.php");

class UserRepository extends BaseRepository
{
    public function GetUserBySteamId($steamId)
    {
        $sql = 'SELECT * FROM '.$this->userTable.' WHERE steamId = ?'; 
        $params = $steamId;

        $this->connect();
        
        $query = $this->dbConnection->prepare($sql);
        $result = $query->execute($params);
        
        
        var_dump($result);die();
        
        $user;
        if($result)
        {
            $user = new \model\SteamUser
            (
            );
        }
        
    }
}