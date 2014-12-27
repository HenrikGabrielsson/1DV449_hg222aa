<?php

namespace model\repository;

require_once("baseRepository.php");

class EbayRepository extends BaseRepository
{
	public function AddMerchandise($merchandiseArr)
	{
		$sql = "INSERT INTO `".$this->merchandiseTable."` (`itemId`, `title`, `imageURL`, `ebayURL`, `location`, `country`, `startTime`, `endTime`, `gameId`)
			VALUES(?,?,?,?,?,?,?,?,?)";

		$this->connect();

		foreach($merchandiseArr as $item)
		{
			$start = $item->GetStartTime();
			$end = $item->GetEndTime();

			$params = array($item->GetItemId(), $item->GetTitle(), $item->GetImageURL(), $item->GetEbayURL(), $item->GetLocation(), $item->GetCountry(), $start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'), $item->GetGameId());

			$query = $this->dbConnection->prepare($sql);
			$query->execute($params);
		}
	}

	public function DeleteMerchandiseItem($merchandiseId)
	{

	}

	public function GetMerchandiseForGame($gameId, $count)
	{

	}
}