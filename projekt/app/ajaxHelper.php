<?php 

require_once("view/suggestView.php");
require_once("model/steamService.php");
require_once("model/ebayService.php");

session_start();

$ebayService = new \model\EbayService();
$steamService = new \model\SteamService();

$suggestView = new \view\SuggestView($ebayService, $steamService);

$merchandise = $suggestView->GetMerchandise($_GET['id']);

$itemsArr;
$itemArr;
foreach ($merchandise as $item) 
{
	$game = $steamService->GetCachedGame($item->GetGameId());
	$itemArr = array("id" => $item->GetId(), "itemId" => $item->GetItemId(), "title" => $item->GetTitle(), "imageURL" => $item->GetImageURL(), "ebayURL" => $item->GetEbayURL(), "location" => $item->GetLocation(), "country" => $item->GetCountry(), "startTime" => $item->GetStartTime(), "endTime" => $item->GetEndTime(), "gameTitle" => $game->GetTitle());
	$itemsArr[] = $itemArr;
}

$retArray = array("merchandise" => $itemsArr, "timeReceived" => time() * 1000);

echo json_encode($retArray);

