<?php

class File
{
	private $base = "/w/web/dco/tools/checkout/";
	
	// constructor
	function __construct($array = null)
	{
	}
	
	// retrieve notice text
	function get($type)
	{
		$file = $this->base.$type;
		$handle = fopen($file,"r");
		if ( !$handle ) return false;
		$contents = fread($handle,filesize($file));
		fclose($handle);
		
		if ( $type == "staff/.htaccess" )
		{
			$array = explode("\n",$contents);
			return substr($array[5],19);
		}
		
		return $contents;
	}
	
	// update notice next
	function update($type,$contents)
	{
		$file = $this->base.$type;
		$handle = fopen($file,"w");
		
		if ( !$handle ) return false;
		
		if ( $type == "staff/.htaccess" )
			$prefix = "SSLRequireSSl\nErrorDocument 503 https://www.eecs.umich.edu/dco/tools/checkout/staff/records.php\n\nCosignProtected On\nAuthType Cosign\nrequire user mcolf ";
		else
			$prefix = "";
		
		$result = !fwrite($handle,$prefix.$contents);
		fclose($handle);
		return true;
	}
	
	// append string to end of file
	function append($type,$contents)
	{
		$file = $this->base.$type;
		$handle = fopen($file,"a");
		if ( !$handle ) return false;
		
		$result = !fwrite($handle,date("c")." ".$contents."\n");
		fclose($handle);
		return true;
	}
	
	// search file for needle
	function search($type,$needle)
	{
		if (stristr($this->get($type),$needle)) return true;
		else return false;
	}

}

?>
