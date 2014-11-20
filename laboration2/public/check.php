<?php
require_once("sec.php");

// check tha POST parameters
$u = $_POST['username'];
$p = $_POST['password'];

// Check if user is OK
if(isset($u) && isset($p) && isUser($u, $p)) {
	// set the session
	sec_session_start();
	$_SESSION['username'] = $u;
	$_SESSION['login_string'] = hash('sha512', "123456" +$u);
    $_SESSION['ip'] = $_SERVER["REMOTE_ADDR"];
    $_SESSION['useragent'] = $_SERVER['HTTP_USER_AGENT'];
	
	header("Location: mess.php"); 
}
else {
	// To bad
	header('HTTP/1.1 401 Unauthorized');
	die("could not call");
}