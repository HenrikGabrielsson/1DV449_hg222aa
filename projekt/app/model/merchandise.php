<?php

namespace model;

//detta är ett föremål som tillhör ett spel (gaming merchandise)
class Merchandise
{
	private $id;
	private $itemId;
	private $title;
	private $imageURL;
	private $ebayURL;
	private $location;
	private $country;
	private $startTime;
	private $endTime;
	private $gameId;

	public function __construct($id, $itemId, $title, $imageURL, $ebayURL, $location, $country, $startTime, $endTime, $gameId)
	{
		$this->id = $id;
		$this->itemId = $itemId;
		$this->title = $title;
		$this->imageURL = $imageURL;
		$this->ebayURL = $ebayURL;
		$this->location = $location;
		$this->country = $country;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->gameId = $gameId;		
	}

	public function GetId()
	{
		return $this->id;
	}
	public function GetItemId()
	{
		return $this->itemId;
	}
	public function GetTitle()
	{
		return $this->title;
	}
	public function GetImageURL()
	{
		return $this->imageURL;
	}
	public function GetEbayURL()
	{
		return $this->ebayURL;
	}
	public function GetLocation()
	{
		return $this->location;
	}
	public function GetCountry()
	{
		return $this->country;
	}
	public function GetStartTime()
	{
		return $this->startTime;
	}
	public function GetEndTime()
	{
		return $this->endTime;
	}
	public function GetGameId()
	{
		return $this->gameId;
	}	
}