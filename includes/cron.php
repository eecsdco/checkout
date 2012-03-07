<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require "checkout.class.php";
require "notice.class.php";
require "helpers.php";

echo "Starting cron session...<br />";
$checkout = new Checkout();
$records = $checkout->record_search("ALL","ALL","ALL","OUT","ALL");

echo "Sending reminders...<br />";
$hour = date("H");
$day = date("N");

// don't run reminders on weekends
if ( $day != "6" AND $day != "7" )
{
	echo "<ul>";
	foreach ( $records as $record )
	{
		echo "<li>".$record["id"]." ";
		
		// way overdue reminders
		if ( $checkout->status($record["id"]) == "way" )
		{
			echo " [WAY OVERDUE] ";
			// send at 8 AM and 2 PM daily
			if ( $hour == "08" OR $hour == "14" )
			{
				$notices = new Notice($record);
				if ( $notices->send("evil.template") ) echo "Sent";
				else echo "Error sending message!";

			}
			else echo "Reminders only sent at 8AM and 2PM!";
		}
		// overdue reminders
		else if ( $checkout->status($record["id"]) == "late" )
		{
			echo " [OVERDUE] ";
			// send at 8 AM daily
			if ( $hour == "08" )
			{
				$notices = new Notice($record);
				if ( $notices->send("overdue.template") ) echo "Sent";
				else echo "Error sending message!";
			}
			else echo "Reminders only sent at 8AM!";
		}
		else echo " (no action needed)";
	}
	echo "</ul>";
} else echo "No reminders on weekends...<br />";
echo "Ending cron session...<br />";