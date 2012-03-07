<?php

die();

echo "STARTING";

error_reporting(E_ALL);
ini_set('display_errors', '1');

require "checkout.class.php";
require "statistics.class.php";
require "notice.class.php";
require "helpers.php"; 

$old_file = "/w/web/dco/tools/checkout/includes/checkout.old.sqlite";

if ( $old_db = new PDO("sqlite:".$old_file) ) echo "connected to database";
else {
	echo "unable to connect to database!";
	die();
}

$checkout = new Checkout();

// import titles
//echo "<h1>IMPORTING TITLES</h1>";
//echo "<ul>";
//$sql = "SELECT * FROM titles ORDER BY id ASC;";
//foreach( $old_db->query($sql) as $title ) {
//	echo "<li>".$title["type"]." - ".$title["title"].": ";
//	$sql = "INSERT INTO titles (id, enable, type, title, notice) VALUES (".$title["id"].", ".$title["enable"].", '".$title["type"]."', '".$title["title"]."', '".$title["notice"]."');";
//	if ( $checkout->db->exec($sql) ) echo "SUCCESS!";
//	else echo "ERROR IMPORTING!";
//}
//echo "</ul>";

// import records
echo "<h1>IMPORTING CHECKOUT RECORDS</h1>";
echo "<ul>";
$sql = "SELECT * FROM records WHERE term = 'long' ORDER BY id ASC;";
foreach ( $old_db->query($sql) as $r ) {
	echo "<li>Record ".$r["id"]." ";
	//if ( $r != "long" ) $term = 7;
	//else $term = 0;
	//if ( $r["status"] == "out" ) $date_in = 0;
	//else $date_in = $r["date_in"];
	//$sql = "INSERT INTO records (id,user,term,title,note,building,agent,date_out,date_in,status)
	//	VALUES (".$r["id"].",'".$r["user"]."',$term,".$r["title"].",'".$r["note"]."','".$r["building"]."','".$r["agent"]."',".$r["date_out"].",$date_in,'".$r["status"]."');";
	//echo $sql;
	//if ( $checkout->db->exec($sql) ) echo "SUCCESS!";
	//else { 
	//	echo "ERROR IMPORTING!<br /><br />";
	//	echo "SQL: ".$sql."<br />"	;
	//	print_r($checkout->db->errorInfo());
	//	echo "<br /><br />";
	//}
}
echo "</ul>";

echo "ENDING";