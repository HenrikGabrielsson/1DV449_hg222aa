<?php

namespace model;

require_once("./configurations.php");

class EbayService
{
	private $ebayUrl = "http://svcs.ebay.com/services/search/FindingService/v1?SERVICE-VERSION=1.13.0&
		OPERATION-NAME=findItemsAdvanced&RESPONSE-DATA-FORMAT=JSON&paginationInput.entriesPerPage=20&
		categoryId=45101,38583&REST-PAYLOAD&		
		";

	public function __construct()
	{

	}

	public function GetProducts($games)
	{
		//hämta ut vilka spel som man ska hämta förslag till på ebay
		$featuredTitles = $this->DecideGameToFeature($games);
		
		$result = file_get_contents($this->ebayUrl . "&SECURITY-APPNAME=" .\Configurations::$EBAY_API_KEY . "&keywords=team+fortress&paginationInput.pageNumber=1");

		var_dump($result);
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