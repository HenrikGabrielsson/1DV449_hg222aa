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

    //sessionen ska ha en "login_string" och ett username.
	if(!isset($_SESSION["username"]) || !isset($_SESSION['login_string']))
    {
        header('HTTP/1.1 401 Unauthorized'); die();
    }

	$user = getUser($_SESSION["username"]);
	$un = $user[0]["username"];

    if($_SESSION['login_string'] !== hash('sha512', "123456" . $un) )
    {
        header('HTTP/1.1 401 Unauthorized'); die();
    }

	else {
		header('HTTP/1.1 401 Unauthorized'); die();
	}
	return true;
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
	$q = "SELECT id FROM users WHERE username = ? AND password = ?";
	$params = array($u, $p);


	$result;
	$stm;
	try {
		$stm = $db->prepare($q);
		$stm->execute($params);
		$result = $stm->fetchAll();
		if(!$result) {
			
			echo "Could not find the user. ";
			return false;
		}
	}
	catch(PDOException $e) {
		echo("Error creating query: " .$e->getMessage());
		return false;
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
	session_end();
	header('Location: index.php');
}

