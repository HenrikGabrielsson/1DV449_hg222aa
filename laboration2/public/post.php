<?php

/**
 * Called from AJAX to add stuff to DB
 */
function addToDB($message, $user) {
    $db = null;


    //se till så att html-taggar i input inte tolkas av en webbläsare
    $message = htmlspecialchars($message);
    $user = htmlspecialchars($user);

    //koll så att har skrivit sitt riktiga namn. Vet inte varför, men så är det.
    if(!isset($_SESSION["username"]) || $_SESSION["username"] != $user )
    {
        return;
    }

    try {
        $db = new PDO("sqlite:../db.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOEcception $e) {
        die("Something went wrong -> " .$e->getMessage());
    }

    $result;

    $q = "INSERT INTO messages (message, name, date) VALUES(?,?,?)";
    $params = array($message, $user, time());
    try {
        $query = $db->prepare($q);
        $result = $query->execute($params);

    }
    catch(PDOException $e)
    {
        return;
    }

}
