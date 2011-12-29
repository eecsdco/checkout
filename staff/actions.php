<?php

require "../includes/checkout.class.php";
require "../includes/statistics.class.php";
require "../includes/notice.class.php";
require "../includes/helpers.php"; 

$checkout = new Checkout();

// set default options
$here = "https://www.eecs.umich.edu/dco/tools/checkout/staff/actions.php";
$message = "WARNING: Required field 'action' not set.";
$error = $message = $notice = $confirm = $reject = "";
$destination = "records.php";
$options = "";
$action = "";

// get parameters
if ( isset($_REQUEST["destination"]) ) $destination = $_REQUEST["destination"];
if ( isset($_REQUEST["options"]) ) $options = $_REQUEST["options"];
if ( isset($_REQUEST["action"]) ) $action = $_REQUEST["action"];

// run actions

if ( $action == "add" ) {
	// check for valid user
	if (valid_umich($_REQUEST["user"])) {
		if ( $checkout->add($_REQUEST["user"],$_REQUEST["item"],$_REQUEST["note"],$_REQUEST["building"],$_SERVER['REMOTE_USER'],$_REQUEST["term"]) )
		{
			// send email to user
			$notices = new Notice($checkout->record_show($checkout->last_id));
			if ( !$notices->send("start.template") ) $error = "An error occured when sending the message.";
			
			// show link to removal form
			$notice = "Record created succesfully.<br /><br /><b>Important!</b> A Request for Removal and Use of University Property form is required for this item. Please print the form using the link below, have the user sign it, and then store it in a safe location. This loan is governed by University of Michigan <a href='http://spg.umich.edu/section/520/'>SPG 520.2</a>.<br /><br /><a href='https://www.eecs.umich.edu/dco/tools/checkout/removal_form.php?id=".$checkout->last_id."' class='button' target='_blank'>Print Form</a> <a href='records.php' class='button'>Close</a>";
		}
		else $error = "An error occured while creating the record.";
	}
	else $error = "The username '".$_REQUEST["user"]."' is not a valid uniquename. Please ensure that you have entered it correctly.";
} 

else if ( $action == "remove" ) {
	// get confirm status
	if (isset($_REQUEST["confirm"])) $confirm = true; 
	else $confirm = false;
	// run action
	if ( $confirm )
	{
		if ( $checkout->remove($_REQUEST["id"]) )
			$message = "Record removed sucessfully.";
		else
			$error = "An error occured while removing the record.";
	}
	else $notice = "This action will completely delete the record. Are you sure you want to continue?<br /><br /><a href='$here?destination=$destination&options=$options&action=remove&id=".$_REQUEST["id"]."&confirm=true' class='button'>Yes</a> <a href='records.php' class='button'>No</a>";
}

else if ( $action == "update" ) {
	if (valid_umich($_REQUEST["user"])) {
		$record_old = $checkout->record_show($_REQUEST["id"]);
		if ( $checkout->update($_REQUEST["id"],$_REQUEST["user"],$_REQUEST["item"],$_REQUEST["note"],$_REQUEST["building"],$_REQUEST["agent"],$_REQUEST["term"]) ) {
			$message = "Record updated sucessfully.";
			// check for changes that require an email update
			if ($_REQUEST["building"] != $record_old["building"] or $_REQUEST["term"] != $record_old["term"] ) {
				$notices = new Notice($checkout->record_show($checkout->last_id));
				if ( !$notices->send("update.template") ) $error = "An error occured when sending the message.";
			}
		}
		else
			$error = "An error occured while updating the record.";
	}
	else $error = "The username '".$_REQUEST["user"]."' is not a valid uniquename. Please ensure that you have entered it correctly.";
}

else if ( $action == "return" ) {
	$record = $checkout->record_show($_REQUEST["id"]);
	if ( $_REQUEST["confirm"] )
	{
		if ( $checkout->checkin($_REQUEST["id"]) )
		{
			$message = "Item returned sucessfully.";
			// send email to user
			$notices = new Notice($checkout->record_show($checkout->last_id));
			if ( !$notices->send("end.template") ) $error = "An error occured when sending the message.";
		}
		else $error = "An error occured while returning the item.";
	}
	else $notice = "Please confirm that <b>".ldap_query($record["user"],"cn")."</b> has returned <b>".$record["type"]." - ".$record["title"]."</b>.<br /><br /><a href='$here?destination=$destination&options=$options&action=return&id=".$_REQUEST["id"]."&confirm=true' class='button'>Confirm</a> <a href='records.php' class='button'>Cancel</a>";
}

// encode variables for URL transport
$message = urlencode($message);
$notice = urlencode($notice);
$error = urlencode($error);

// send user back to starting page
header("Location: https://www.eecs.umich.edu/dco/tools/checkout/staff/$destination?message=$message&error=$error&notice=$notice&$options");

?>