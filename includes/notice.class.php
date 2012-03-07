<?php

// REQUIRES
//require "checkout.class.php";
//require "helpers.php";

class Notice
{
	private $record;
	private $checkout = null;
	private $base = "/w/web/dco/tools/checkout/includes/";
	
	// constructor
	function __construct($array = null)
	{
		$this->record = $array;
	}
	
	// retrieve notice text
	function get($type)
	{
		$file = $this->base.$type;
		$handle = fopen($file,"r");
		if ( !$handle ) return false;
		$contents = fread($handle,filesize($file));
		fclose($handle);
		return $contents;
	}
	
	// update notice next
	function update($type,$contents)
	{
		$file = $this->base.$type;
		$handle = fopen($file,"w");
		if ( !$handle ) return false;
		$result = !fwrite($handle,$contents);
		fclose($handle);
		return true;
	}
	
	function fill_blanks($type)
	{
		if ( $this->checkout == null ) $this->checkout = new Checkout();
		
		// read in template from file
		$file = $this->base.$type;
		$handle = fopen($file,"r");
		if ( !$handle ) echo "handle is null";
		$contents = fread($handle,filesize($file));
		fclose($handle);
		
		//determine title
		$id = $this->record["title_id"];
		$title = $this->checkout->titles->type($id)." - ".$this->checkout->titles->title($id);
		
		// determine term
		if ( $this->record["term"] == 0 ) $term = "long term";
		else $term = $this->record["term"]." day";
		
		// determine due date
		if ( $this->record["term"] > 0 ) {
			$due_date_no_text = date("l F, j Y",$this->record["date_out"]+($this->record["term"]*86400));
			$due_date = "on ".$due_date_no_text;
		}
		else $due_date = "at some negotioated date in the future (long term loan)";
		
		// determine model
		$model = $this->checkout->titles->model($id);
		
		// determine cost
		$cost = "$".$this->checkout->titles->cost($id);
		
		// determine name, show blank if unable to find
		if ( strlen($name = ldap_query($this->record["user"],"cn")) < 2 ) {
			if ( $type == "removal.template" ) $name = "__________________________________________";
			else $name = $this->record["user"];
		}
		
		// replace variables
		$contents = str_replace("{{user}}",$this->record["user"],$contents);
		$contents = str_replace("{{name}}",$name,$contents);
		$contents = str_replace("{{building}}",$this->record["building"],$contents);
		$contents = str_replace("{{title}}",$title,$contents);
		$contents = str_replace("{{title_notice}}",$this->checkout->titles->notice($this->record["title_id"]),$contents);
		$contents = str_replace("{{term}}",$term,$contents);
		$contents = str_replace("{{date_out}}",date("l F, j Y",$this->record["date_out"]),$contents);
		$contents = str_replace("{{account_link}}","https://www.eecs.umich.edu/dco/tools/checkout/account.php",$contents);
		$contents = str_replace("{{karma}}",$this->checkout->karma($this->record["user"]),$contents);
		$contents = str_replace("{{due_date}}",$due_date,$contents);
		$contents = str_replace("{{due_date_no_text}}",$due_date_no_text,$contents);
		$contents = str_replace("{{model}}",$model,$contents);
		$contents = str_replace("{{cost}}",$cost,$contents);
		
		return $contents;
	}
	
	// send notice by email
	function send($type)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	
		// disable sending emails
		// return true;
		
		$this->checkout = new Checkout();
		
		// cannot send when checkout record is null
		if ( $this->record == null ) echo "record is null";
		
		$contents = $this->fill_blanks($type);
		
		$headers = "From: DCO Checkout System <help@eecs.umich.edu>\r\n"."X-Mailer: php";
		$subject = "[DCO Checkout] ";
		
		if ( $type == "start.template" ) $subject .= "Loan Confirmation";
		else if ( $type == "update.template" ) $subject .= "Loan Terms Updated";
		else if ( $type == "end.template" ) $subject .= "Return Confirmation";
		else if ( $type == "overdue.template" ) $subject .= "Please return ".$this->record["type"]." - ".$this->record["title"];
		else if ( $type == "evil.template" ) $subject .= "Return ".$this->record["type"]." - ".$this->record["title"]." ASAP";
		else $subject .= "Notice";
		
		if ( valid_umich($this->record["user"]) ) {
			$to = $this->record["user"]."@umich.edu";
			$result = false;
			$result = mail($to,$subject,$contents,$headers);
			
			//echo "Sending email. Contents as follows:<br />";
			//echo "TO: ".$to."<br />";
			//echo "Subject: ".$subject."<br />";
			//echo "Headers: ".$headers."<br />";
			//echo "Contents: ".$contents."<br />";
			
			//if ( $result ) echo "Success sending to $to";
			//else die("Unable to send to $to!");
			
			// update log 
			$log = new Log();
			if ( $result === true ) $result_string = " [SUCCESS]";
			else $result_string = " [FAILURE]";
			if ( $type == "overdue.template" ) $log->write("REMINDER ".$this->record["id"]." ".$this->record["user"]." ".$this->record["type"]." - ".$this->record["title"]." TO ".$to.$result_string);
			if ( $type == "evil.template" ) $log->write("EVIL-REMINDER ".$this->record["id"]." ".$this->record["user"]." ".$this->record["type"]." - ".$this->record["title"]." TO ".$to.$result_string);
			
		}
		if ( valid_eecs($this->record["user"]) ) {
			$to = $this->record["user"]."@eecs.umich.edu";
			$result = false;
			$result = mail($to,$subject,$contents,$headers); 
			
			//echo "Sending email. Contents as follows:<br />";
			//echo "TO: ".$to."<br />";
			//echo "Subject: ".$subject."<br />";
			//echo "Headers: ".$headers."<br />";
			//echo "Contents: ".$contents."<br />";
			
			//if ( $result ) echo "Success sending to $to";
			//else die("Unable to send to $to!");
			
			// update log
			$log = new Log();
			if ( $result === true ) $result_string = " [SUCCESS]";
			else $result_string = " [FAILURE]";
			if ( $type == "overdue.template" ) $log->write("REMINDER ".$this->record["id"]." ".$this->record["user"]." ".$this->record["type"]." - ".$this->record["title"]." TO ".$to.$result_string);
			if ( $type == "evil.template" ) $log->write("EVIL-REMINDER ".$this->record["id"]." ".$this->record["user"]." ".$this->record["type"]." - ".$this->record["title"]." TO ".$to.$result_string);
			
		}
		
		return $result;
		
	}

}