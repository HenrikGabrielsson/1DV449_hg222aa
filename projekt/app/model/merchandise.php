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
		return strip_tags($this->id);
	}
	public function GetItemId()
	{
		return strip_tags($this->itemId);
	}
	public function GetTitle()
	{
		return strip_tags($this->title);
	}
	public function GetImageURL()
	{
		return strip_tags($this->imageURL);
	}
	public function GetEbayURL()
	{
		return strip_tags($this->ebayURL);
	}
	public function GetLocation()
	{
		return strip_tags($this->location);
	}
	public function GetCountry()
	{
		return strip_tags($this->country);
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
		return strip_tags($this->gameId);
	}	
}