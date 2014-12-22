<?php 

namespace model\repository;

class BaseRepository
{
    protected $dbConnection;
    
    public function connect()
    {
        //kollar ifall det inte redan finns en anslutning till databasen så skapas den här
        if($this->dbConnection === NULL)
        {
            try
            {
                $this->dbConnection  = new \PDO(\Configurations::$connectionString, \Configurations::$dbUserName,\Configurations::$dbPassword);
            }
            catch(Exception $e)
            {
                throw new Exception("Problems with the database connection.");
            }
        }
    }
}