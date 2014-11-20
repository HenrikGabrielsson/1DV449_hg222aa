<?php

/**
* Called from AJAX to add stuff to DB
*/
function addToDB($message, $user) {
	$db = null;

    //se till så att html-taggar i input inte tolkas av en webbläsare
    $message = htmlspecialchars($message);
    $user = htmlspecialchars($user);
	
	try {
		$db = new PDO("sqlite:../db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOEcception $e) {
		die("Something went wrong -> " .$e->getMessage());
	}

	$q = "SELECT * FROM users WHERE username = ?";
	$params = array($user);
	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute($params);
		$result = $stm->fetchAll();
		if(!$result) {
			return "Could not find the user";
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	
	$q = "INSERT INTO messages (message, name, date) VALUES(?,?,?)";
	$params = array($message, $user, microtime());
	try {
		$query = $db->prepare($q);
		$result = $query->execute($params);

	}
	catch(PDOException $e) 
	{
		return;
	}
	

	// Send the message back to the client
	echo "Message saved by user: " .json_encode($result);
	
}

