<?php

//just for testing
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once("controller/masterController.php");

$masterController = new \controller\MasterController();

$masterController->GetPage();