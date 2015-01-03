<?php 

require_once("./view/suggestView.php");

$suggestView = new \view\SuggestionView(new \model\SteamService(), new \model\EbayService());

echo $suggestView->GetMerchandise();
