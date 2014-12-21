<?php 

namespace controller;

//interface för de controllers som ska fylla views med innehåll.
interface IContentController
{
    public function GetContent();
    public function GetTitle();
}