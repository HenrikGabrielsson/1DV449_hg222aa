<?php

require_once("controller/masterController.php");

//Skapar MasterController och hämtar en sida åt användaren
$masterController = new \controller\MasterController();
$masterController->GetPage();