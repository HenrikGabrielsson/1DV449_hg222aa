<?php

/**
Just som simple scripts for session handling
*/
function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params(3600, $cookieParams["path"], $cookieParams["domain"], $secure, false);
        $httponly = true; // This stops javascript being able to access the session id.
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(); // regenerated the session, delete the old one.
}

function checkUser() {

	if(!session_id()) {
		sec_session_start();
	}

	//kollar så det finns en sessionsvariabel med användarnamn
	if(!isset($_SESSION["username"]))
    {
        header('HTTP/1.1 401 Unauthorized'); die();
    }


    if($_SERVER["REMOTE_ADDR"] != $_SESSION['ip'] || $_SERVER["HTTP_USER_AGENT"] != $_SESSION['useragent'])
    {
    	header('HTTP/1.1 401 Unauthorized'); die();
    }
}

function isUser($u, $p) {
	$db = null;

	try {
		$db = new PDO("sqlite:../db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die("Del -> " .$e->getMessage());
	}

	$sql = "SELECT salt FROM users WHERE username = ?";
	$params = array($u);

	$query = $db->prepare($sql);
	$query->execute($params);
	$salt = $query->fetch()[0];


	//om användaren inte finns så returneras false.
	if(!$salt)
	{
		echo "could not find the user. ";
		return false;
	}	

	//matcha lösenord mot det i databasen
	$sql = "SELECT id FROM users WHERE username = ? AND password = ?";
	$params = array($u, strtoupper(hash("sha512", $p . $salt)));

	$query = $db->prepare($sql);
	$query->execute($params);
	$result = $query->fetchAll();


	if(!$result) 
	{		
		echo "Could not find the user. ";
	}

	return $result;
}

function getUser($user) {
	$db = null;

	try {
		$db = new PDO("sqlite:../db.db");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		die("Del -> " .$e->getMessage());
	}
	$q = "SELECT * FROM users WHERE username = ?";
	$params = array($user);

	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute($params);
		$result = $stm->fetchAll();
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
	}

	return $result;
}


function logout() {

	if(!session_id()) {
		sec_session_start();
	}
	session_unset(); 
	session_destroy(); 
	
	header('Location: index.php');
}

