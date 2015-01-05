<?php

namespace model\repository;

require_once("baseRepository.php");

class EbayRepository extends BaseRepository
{

	//Lägger till all merchandise i databasen.
	public function AddMerchandise($merchandiseArr)
	{
		//om array är tom körs inte metoden.
		if($merchandiseArr === null)
		{
			return;
		}

		$sql = "INSERT INTO `".$this->merchandiseTable."` (`itemId`, `title`, `imageURL`, `ebayURL`, `location`, `country`, `startTime`, `endTime`, `gameId`)
			VALUES(?,?,?,?,?,?,?,?,?)";

		//lägger till dem varför sig.
		foreach($merchandiseArr as $item)
		{
			$start = $item->GetStartTime();
			$end = $item->GetEndTime();

			$params = array($item->GetItemId(), $item->GetTitle(), $item->GetImageURL(), $item->GetEbayURL(), $item->GetLocation(), $item->GetCountry(), $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $item->GetGameId());

			$this->RunQuery($sql, $params);
		}
	}

	//ta bort ett föremål från databasen.
	public function DeleteMerchandiseItem($itemId)
	{
		$sql = "DELETE FROM `".$this->merchandiseTable."` WHERE id=?";
		
		$this->RunQuery($sql, array($itemId));
	}

	//ta bort alla föremål som tillhör ett visst spel.
	public function DeleteMerchandiseForGame($gameId)
	{
		$sql = $sql = "DELETE FROM `".$this->merchandiseTable."` WHERE gameId=?";

		$this->RunQuery($sql, array($gameId));
	}

	//hämtar alla föremål som hör till ett visst spel.
	public function GetMerchandiseForGame($gameId, $count)
	{
		$sql = "SELECT * FROM `".$this->merchandiseTable."` WHERE gameId=? ";
		$result = $this->RunQuery($sql, array($gameId));

		//plocka ut n slumpmässigt valda items.
		shuffle($result);
		$result = array_slice($result, 0, $count);

		//avslutar om det inte fanns något i databasen.
		if(!$result)
		{
			return null;
		}

		$merchandise = array();
		foreach ($result as $item) 
		{
			$merchandise[] = new \model\Merchandise
			(
				$item["id"],
				$item["itemId"],
				$item["title"],
				$item["imageURL"],
				$item["ebayURL"],
				$item["location"],
				$item["country"],
				new \DateTime($item["startTime"]),
				new \DateTime($item["endTime"]),
				$gameId
			);
		}

		return $merchandise;
	}
}