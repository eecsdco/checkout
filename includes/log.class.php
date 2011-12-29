<?php

class Log
{
	// constructor
	function __construct()
	{
	}
	
	// append string to end of file
	public function write($contents)
	{
		$handle = fopen("/w/web/dco/tools/checkout/checkout.log","a");
		if ( !$handle ) return false;
		$result = !fwrite($handle,date("c")." ".$contents."\n");
		fclose($handle);
		return true;
	}
	
	public function search($term) 
	{
		$handle = fopen("/w/web/dco/tools/checkout/checkout.log","r");
		$results = array();
		while ( $line = fgets($handle) ) 
		{
			if (stristr($line,$term) || $term == "ALL") $results[] = $line;
		}
		fclose($handle);
		return $results;
	}

}

?>
