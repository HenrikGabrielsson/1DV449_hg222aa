<?php

namespace model\repository;

require_once("baseRepository.php");

class EbayRepository extends BaseRepository
{
	public function AddMerchandise($merchandiseArr)
	{
		if($merchandiseArr === null)
		{
			return;
		}

		$sql = "INSERT INTO `".$this->merchandiseTable."` (`itemId`, `title`, `imageURL`, `ebayURL`, `location`, `country`, `startTime`, `endTime`, `gameId`)
			VALUES(?,?,?,?,?,?,?,?,?)";

		foreach($merchandiseArr as $item)
		{
			$start = $item->GetStartTime();
			$end = $item->GetEndTime();

			$params = array($item->GetItemId(), $item->GetTitle(), $item->GetImageURL(), $item->GetEbayURL(), $item->GetLocation(), $item->GetCountry(), $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $item->GetGameId());

			$this->RunQuery($sql, $params);
		}
	}

	public function DeleteMerchandiseItem($itemId)
	{
		$sql = "DELETE FROM `".$this->merchandiseTable."` WHERE id=?";
		
		$this->RunQuery($sql, array($itemId));
	}

	public function DeleteMerchandiseForGame($gameId)
	{
		$sql = $sql = "DELETE FROM `".$this->merchandiseTable."` WHERE gameId=?";

		$this->RunQuery($sql, array($gameId));
	}

	public function GetMerchandiseForGame($gameId, $count)
	{
		$sql = "SELECT * FROM `".$this->merchandiseTable."` WHERE gameId=? ";
		$result = $this->RunQuery($sql, array($gameId));

		//plocka ut n slumpmässigt valda items.
		shuffle($result);
		$result = array_slice($result, 0, $count);

		if(!$result)
		{
			return null;
		}

		$merchandise = array();
		foreach ($result as $item) 
		{

			//($id, $itemId, $title, $imageURL, $ebayURL, $location, $country, $startTime, $endTime, $gameId)
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