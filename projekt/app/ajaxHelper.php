<?php 

require_once("view/suggestView.php");
require_once("model/steamService.php");
require_once("model/ebayService.php");

if(isset($_GET["function"]))
{
	//användaren vill hämta merchandise.
	if($_GET["function"] === "getMerchandise")
	{
		try
		{
			session_start();

			$ebayService = new \model\EbayService();
			$steamService = new \model\SteamService();

			$suggestView = new \view\SuggestView($ebayService, $steamService);
			
			//avbryter om användarens token inte stämmer överens med den som kom som get-parameter
			if(!$steamService->CheckSecurityToken($_GET["token"]))
			{
				echo "";
			}

			$merchandise = $suggestView->GetMerchandise($_GET['id']);

			//hämtar all merchandise och gör om objekten till arrayer för att gör om de till JSON-objekt.
			$itemsArr = array();
			$itemArr;
			
			if(isset($merchandise))
			{
				foreach ($merchandise as $item) 
				{
					$game = $steamService->GetCachedGame($item->GetGameId());
					$itemArr = array("id" => $item->GetId(), "itemId" => $item->GetItemId(), "title" => $item->GetTitle(), "imageURL" => $item->GetImageURL(), "ebayURL" => $item->GetEbayURL(), "location" => $item->GetLocation(), "country" => $item->GetCountry(), "startTime" => $item->GetStartTime(), "endTime" => $item->GetEndTime(), "gameTitle" => $game->GetTitle());
					$itemsArr[] = $itemArr;
				}		
			}

			$retArray = array("merchandise" => $itemsArr, "timeReceived" => time() * 1000);
			echo strip_tags(json_encode($retArray));
		}	
		catch(Exception $e)
		{
			echo "";
		}
	}

	//en snabb koll om man fortfarande kommer åt servern.
	else if($_GET["function"] === "ping")
	{
		echo 1;
	}
}