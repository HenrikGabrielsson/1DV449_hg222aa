<?php

namespace model;

require_once("merchandise.php");
require_once("./configurations.php");
require_once("./model/repository/ebayRepository.php");

class EbayService
{
	private $ebayUrl = "http://svcs.ebay.com/services/search/FindingService/v1?SERVICE-VERSION=1.13.0&
		OPERATION-NAME=findItemsAdvanced&RESPONSE-DATA-FORMAT=JSON&categoryId=38583&REST-PAYLOAD";

	private $ebayRepo;

	public function __construct()
	{
		$this->ebayRepo = new \model\repository\EbayRepository();
	}

	public function GetProducts($games)
	{
		//hämta ut vilka spel som man ska hämta förslag till på ebay
		$featuredTitles = $this->DecideGameToFeature($games);

		$merchandise = array();
		foreach($featuredTitles as $gameTitle => $count)
		{
			$result = json_decode(file_get_contents($this->ebayUrl . "&SECURITY-APPNAME=" .\Configurations::$EBAY_API_KEY . "&keywords=". str_replace(" ", "+", $gameTitle) ."&paginationInput.entriesPerPage=" . 10*$count ), true);	

			$items = $result["findItemsAdvancedResponse"][0]["searchResult"][0]["item"];
			
			foreach ($items as $item) 
			{
				$thisGameId;
				foreach ($games as $game) 
				{
					if($game->GetTitle() == $gameTitle)
					{
						$thisGameId = $game->GetId();
						break;
					}
				}

				$merchandise[] = new Merchandise
				(
					null, 
					$item["itemId"][0],
					$item["title"][0],
					$this->saveImageLocally($item["galleryURL"][0],$item["itemId"][0]),
					$item["viewItemURL"][0],
					$item["location"][0],
					$item["country"][0],
					new \DateTime($item["listingInfo"][0]["startTime"][0]),
					new \DateTime($item["listingInfo"][0]["endTime"][0]),
					$thisGameId
				);
			}
		}

		$this->ebayRepo->AddMerchandise($merchandise);
	
	}

	private function SaveImageLocally($remoteURL, $itemId)
    {
        $local = "model/merch/" . $itemId . ".jpg";

        file_put_contents($local, file_get_contents($remoteURL));

        return $local;
    }

	private function DecideGameToFeature($games)
	{

		$gamesByPoint = array();

		$overallPlaytimeWeight = 0.2;
		$recentPlaytimeWeight = 2.5;

		foreach ($games as $game) 
		{
			$gamesByPoint[$game->GetTitle()] = (int)$game->GetOverallPlaytime() * $overallPlaytimeWeight + (int)$game->GetRecentPlaytime() * $recentPlaytimeWeight;
		}
		$scoreSum = array_sum($gamesByPoint);
		asort($gamesByPoint, SORT_NUMERIC);

		$gamesByChance = array();

		$current = 0;
		foreach ($gamesByPoint as $game => $score) 
		{
			$current  += $score != 0 ? $score/$scoreSum : 0;
			$gamesByChance[$game] = $current;
		}

		$chosenGames = array();

		$keys = array_keys($gamesByChance);

		for($i = 0; $i < 10; $i++)
		{	
			$rand = mt_rand()/mt_getrandmax();

			for($j = 0; $j < count($gamesByChance); $j++)
			{
				if($rand < $gamesByChance[$keys[$j]] && $rand > $gamesByChance[$keys[$j-1]])
				{
					$chosenGames[] = $keys[$j];
					break;
				}
			}
		}

		return array_count_values($chosenGames);
	}
}