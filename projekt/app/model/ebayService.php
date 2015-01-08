<?php

namespace model;

require_once("merchandise.php");
require_once("./configurations.php");
require_once("./model/repository/ebayRepository.php");
require_once("./model/repository/steamRepository.php");

class EbayService
{
	//url för att använda EBay's API.
	private $ebayUrl = "http://svcs.ebay.com/services/search/FindingService/v1?SERVICE-VERSION=1.13.0&
		OPERATION-NAME=findItemsAdvanced&RESPONSE-DATA-FORMAT=JSON&categoryId=38583&REST-PAYLOAD";

	private $ebayRepo;
	private $steamRepo;

	public function __construct()
	{
		$this->ebayRepo = new \model\repository\EbayRepository();
		$this->steamRepo = new \model\repository\SteamRepository();
	}

	//hämtar en lista med produkter som ska visas på användaren beroende på vilka spel som ägs av spelaren.
	public function GetProducts($games)
	{
		//har inga spel
		if(count($games) == 0)
		{
			return;
		}

		//hämta ut vilka spel som man ska hämta förslag till på ebay
		$featuredTitles = $this->DecideGameToFeature($games);

		$merchandise = array();
		foreach($featuredTitles as $gameTitle => $count)
		{
			//hämtar spel efter titel. Lite klumpigt..
			$thisGame;
			foreach ($games as $game) 
			{
				if($game->GetTitle() == $gameTitle)
				{
					$thisGame = $game;
					break;
				}
			}
			$lastUpdate = $thisGame->GetLastMerchandiseUpdate();

			//kollar om spelet har uppdaterats med nya produkter nyligen.
			if(isset($lastUpdate))
			{
				$lastUpdate = new \DateTime($thisGame->GetLastMerchandiseUpdate());
			}

			//om spelets merchandise aldrig hämtats eller om det var mer än ett dygn sen senast så hämtas det igen från ebay.
			if($lastUpdate == null || $lastUpdate->add(new \DateInterval('P1D')) < new \Datetime())
			{
				//ta bort gamla produkter.
				$this->ebayRepo->DeleteMerchandiseForGame($thisGame->GetId());

				//hämtar nya och cachar.
				$this->ebayRepo->AddMerchandise($this->GetProductsFromEbay($thisGame));
				$thisGame->SetLastMerchandiseUpdate(new \DateTime());
				$this->steamRepo->UpdateGame($thisGame);
			}

			//hämtar det antal som behövs från databas (mindre om det inte finns tillräckligt.)
			$merchandiseFromDb = $this->ebayRepo->GetMerchandiseForGame($thisGame->GetId(), $count * 10);

			if(isset($merchandiseFromDb))
			{
				$merchandise = array_merge($merchandise, $merchandiseFromDb);
			}
		}

		//auktioner på ebay som redan tagit slut tas bort här.
		foreach ($merchandise as $item) 
		{
			if($item->GetEndTime() < new \Datetime())
			{
				$this->ebayRepo->DeleteMerchandiseItem($item->GetId());
			}
		}

		return $merchandise;
	}

	//här hämtas produkter för ett visst spel från ebay.
	private function GetProductsFromEbay($game)
	{
		$result = json_decode(file_get_contents($this->ebayUrl . "&SECURITY-APPNAME=" .\Configurations::$EBAY_API_KEY . "&keywords=". str_replace(" ", "+", $game->GetTitle()) ."&paginationInput.entriesPerPage=50"), true);	

		//inga produkter hittades, returnerar null.
		if($result["findItemsAdvancedResponse"][0]["searchResult"][0]["@count"] == "0")
		{
			return null;
		}

		$items = $result["findItemsAdvancedResponse"][0]["searchResult"][0]["item"];		
		$merchandise = array();

		//returnerar array med all mercahndise.
		foreach ($items as $item) 
		{
			$merchandise[] = new Merchandise
			(
				null, 
				$item["itemId"][0],
				$item["title"][0],
				$this->saveImageLocally($item["galleryURL"][0], $item["itemId"][0]),
				$item["viewItemURL"][0],
				$item["location"][0],
				$item["country"][0],
				new \DateTime($item["listingInfo"][0]["startTime"][0]),
				new \DateTime($item["listingInfo"][0]["endTime"][0]),
				$game->GetId()
			);
		}
		return $merchandise;
	}
	
	//sparar en bild på ett föremål lokalt.
	private function SaveImageLocally($remoteURL, $itemId)
    {
        $local = "model/merch/" . $itemId . ".jpg";

        file_put_contents($local, file_get_contents($remoteURL));

        return $local;
    }

    //denna funktionen väljer ut vilka spel som ska användas för att hitta resultat på ebay.
	private function DecideGameToFeature($games)
	{
		$gamesByPoint = array();

		//spel som spelas ofta, och spel som spelats nyligen får lite förtur.
		$overallPlaytimeWeight = 0.2;
		$recentPlaytimeWeight = 2.5;

		//bestämmer chansen för att ett spel ska användas för att hämta produkter.
		foreach ($games as $game) 
		{
			$gamesByPoint[$game->GetTitle()] = (int)$game->GetOverallPlaytime() * $overallPlaytimeWeight + (int)$game->GetRecentPlaytime() * $recentPlaytimeWeight;
		}
		$scoreSum = array_sum($gamesByPoint);
		asort($gamesByPoint, SORT_NUMERIC);


		//ändrar om poängen som räknades ut till procent.
		$gamesByChance = array();

		$current = 0;
		foreach ($gamesByPoint as $game => $score) 
		{
			$current  += $score != 0 ? $score/$scoreSum : 0;
			$gamesByChance[$game] = $current;
		}

		$chosenGames = array();
		$keys = array_keys($gamesByChance);

		//här skapas arrayen med spel som ska användas samt hur många produkter som ska hämtas per spel 
		//(om det finns produkter att hämta i det antalet.)
		for($i = 0; $i < 10; $i++)
		{	
			$rand = mt_rand()/mt_getrandmax();

			for($j = 0; $j < count($gamesByChance); $j++)
			{
				if(count($gamesByChance) == 1 || ($rand < $gamesByChance[$keys[$j]] && $rand > $gamesByChance[$keys[$j-1]]))
				{
					$chosenGames[] = $keys[$j];
					break;
				}
			}
		}
		return array_count_values($chosenGames);
	}
}