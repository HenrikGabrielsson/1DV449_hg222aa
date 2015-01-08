<?php 

namespace controller;

require_once("./view/errorView.php");

class ErrorController implements IContentController
{
	private $view;

	public function __construct()
	{
		$this->view = new \view\ErrorView();
	}

	//Hämta sidans titel
	public function GetTitle()
	{
		return $this->view->GetTitle();
	}

	//Hämta sidans innehåll
	public function GetContent()
	{
		return $this->view->GetContent();
	}
}