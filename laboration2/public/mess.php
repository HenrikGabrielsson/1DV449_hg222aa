<?php
	require_once("get.php");
    require_once("sec.php");

    //kollar så användaren har en sessionskaka.
    checkUser();

    $_SESSION["token"] = md5(uniqid());
?>
<!DOCTYPE html>
<html lang="sv">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="apple-touch-icon" href="touch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad-retina.png">
    <link rel="shortcut icon" href="pic/favicon.png">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="css/dyn.css" />

    
	<title>Messy Labbage</title>
  </head>       

  <body>
        <div id="container">
            
            <div id="messageboard">
                <form action="functions.php?function=logout" method="post" id="addMessage">
                <input class="btn btn-danger" type="submit" id="buttonLogout" value="Logout" style="margin-bottom: 20px;" />
                </form>

                <div id="messagearea"></div>
                
                <p id="numberOfMess">Antal meddelanden: <span id="nrOfMessages">0</span></p>
                <label for="inputName">Name:</label> <input id="inputName" type="text" name="name" /><br />
                <label for="inputText">Message:</label>
                <textarea name="mess" id="inputText" cols="55" rows="6"></textarea>
                <input type="hidden" name="token" id="hiddenToken" <?php echo 'value="'.$_SESSION["token"].'"' ?>>
                <input class="btn btn-primary" type="button" id="buttonSend" value="Write your message" />
                <span class="clear">&nbsp;</span>

            </div>

        </div>

        <script type="text/javascript" src="js/longpoll.js"></script>
        <script src="js/MessageBoard.js"></script>
        <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<script src="js/bootstrap.js"></script>

	</body>
	</html>