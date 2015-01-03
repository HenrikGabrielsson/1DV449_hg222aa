<?php 

require_once("view/suggestView.php");
require_once("model/steamService.php");
require_once("model/ebayService.php");

session_start();

$suggestView = new \view\SuggestView(new \model\EbayService(), new \model\SteamService());

echo $suggestView->GetMerchandise($_GET['id']);
