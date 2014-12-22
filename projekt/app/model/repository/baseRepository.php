<?php 

namespace model\repository;

require_once("./configurations.php");

class BaseRepository
{
    protected $dbConnection;
    
    //tabellnamn
    protected $userTable = "steam.user";
    protected $friendshipTable = "steam.friendship";
    protected $gameOwnershipTable = "steam.gameOwnership";
    protected $gameTable = "steam.game";
    protected $avatarTable = "steam.avatar";
    
    public function connect()
    {
        //kollar ifall det inte redan finns en anslutning till databasen så skapas den här
        if($this->dbConnection === NULL)
        {
            try
            {
                $this->dbConnection  = new \PDO(\Configurations::$CONNECTION_STRING, \Configurations::$DB_USERNAME,\Configurations::$DB_PASSWORD);
            }
            catch(Exception $e)
            {
                throw new Exception("Problems with the database connection.");
            }
        }
    }
}