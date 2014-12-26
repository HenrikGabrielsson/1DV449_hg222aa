<?php

namespace model;

class Merchandise
{
	private $id;
	private $itemId;
	private $title;
	private $imageURL;
	private $ebayURL;
	private $location;
	private $country;
	private $buyNowPrice;
	private $startTime;
	private $endTime;
	private $gameId;

	public function __construct($id, $itemId, $title, $imageURL, $ebayURL, $location, $country, $buyNowPrice, $startTime, $endTime, $gameId)
	{
		$this->id = $id;
		$this->itemId = $itemId;
		$this->title = $title;
		$this->imageURL = $imageURL;
		$this->ebayURL = $ebayURL;
		$this->location = $location;
		$this->country = $country;
		$this->buyNowPrice = $buyNowPrice;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->gameId = $gameId;		
	}

	
}