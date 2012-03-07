<?php

require "checkout.class.php";
require "notice.class.php";

class Cron
{
	private $checkout
	private $notices;
	
	// constructor
	function __construct()
	{
		$this->checkout = new Checkout();
		$this->titles = new Title();
	}
	
	function hourly()
	{
	
	}

}