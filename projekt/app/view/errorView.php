<?php

namespace view;

class ErrorView
{
	public function GetTitle()
	{
		return "Oops";
	}

	public function GetContent()
	{
		return 
		'
			<h1>Oops! Something went wrong</h1>
			<p>An unknown error occurred and we couldn\t get you the content that you wanted. Please try again in a little while</p>
 		';
	}
}