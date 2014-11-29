<?php

// get the specific message
function getMessages() {
	$db = null;

	try {
		$db = new PDO("sqlite:../db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die("Del -> " .$e->getMessage());
	}
	
	$q = "SELECT * FROM messages";
	
	$result;
	$stm;	
	try {
		$stm = $db->prepare($q);
		$stm->execute();
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
		echo("Error");
		return false;
	}
	
	if($result)
		return $result;
	else
	 	return false;
}

//tar ett datum och hämtar alla messages som kommit in efter det.
function getMessagesSince($timeStamp)
{
    $db = null;

    try
    {
        $db = new PDO("sqlite:../db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
        echo ("Error");
    }

    $q = "SELECT * FROM messages WHERE CAST(date AS INTEGER)  > ?";
    $params = array($timeStamp);

    try
    {
        $stm = $db->prepare($q);
        $stm->execute($params);
        $result = $stm->fetchAll();
    }
    catch(PDOException $e)
    {
        echo "Error";
        return false;
    }

    return $result;
}

//longpolling
function getAnyNewMessages($timeStamp)
{
    $timeSinceRequest = 0;

    while($timeSinceRequest < 20)
    {
        $newMessages = getMessagesSince($timeStamp);


        if(count($newMessages) > 0)
        {
            break;
        }
        sleep(1);

        $timeSinceRequest++;
    }

    return $newMessages;

}
