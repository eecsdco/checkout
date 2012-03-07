<?php

require "title.class.php";
require_once "log.class.php";

class Checkout
{
	public $db;
	public $titles;
	public $last_id;
	
	// constructor
	function __construct()
	{
		$this->connect();
	}
	
	// connect to database and setup table
	private function connect()
	{
		date_default_timezone_set("America/Detroit");
		$this->titles = new Title();
		//$this->db = & $this->titles->db;
		
		$file = "/w/web/dco/tools/checkout/includes/checkout.sqlite";
		
		try {
			$this->db = new PDO("sqlite:".$file);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		} catch (PDOException $e) {
			echo "PDO Connection Error: ".$e->getMessage();
			die("Unable to connect!");
		}
		
		if ( $this->db )
		{
			$sql = "CREATE TABLE IF NOT EXISTS records( ";
			$sql = $sql."id INTEGER PRIMARY KEY AUTOINCREMENT, ";
			$sql = $sql."user TEXT NOT NULL, ";
			$sql = $sql."term INTEGER NOT NULL DEFAULT 7, ";
			$sql = $sql."title INTEGER NOT NULL, ";
			$sql = $sql."note TEXT, ";
			$sql = $sql."building TEXT NOT NULL, ";
			$sql = $sql."agent TEXT NOT NULL, ";
			$sql = $sql."date_out INTEGER NOT NULL, ";
			$sql = $sql."date_in INTEGER, ";
			$sql = $sql."status TEXT NOT NULL DEFAULT 'out' )";
			$rows = $this->db->exec($sql);
		} 
		else die( "Database does not exist." );
	}
	
	// add a new record
	public function add($user,$title,$note,$building,$agent,$term = "short")
	{
		//die("user: $user, title: $title, note: $note, building: $building, agent: $agent, term: $term");
		
		
		
		if ( $user != "" AND $term != "" AND $title != "" AND $building != "" AND $agent != "" )
		{
			$sql = "INSERT INTO records (user, term, title, note, building, agent, date_out) VALUES (:user,:term,:title,:note,:building,:agent,:time);";
			$statement = $this->db->prepare($sql);
			$statement->bindParam(":user",$user);
			$statement->bindParam(":term",$term);
			$statement->bindParam(":title",$title);
			$statement->bindParam(":note",$note);
			$statement->bindParam(":building",$building);
			$statement->bindParam(":agent",$agent);
			$statement->bindParam(":time",time());
			if ( !$statement->execute() ) {
				echo "UNABLE TO INSERT :( ";
				//$statement->debugDumpParams();
				echo "<br /><br />SQL Error: ".$statement->errorCode()." Text: ";
				print_r($statement->errorInfo());
				return FALSE;
			}
		} else return false;
		
		// get last effected ID
		$this->last_id = $this->db->lastInsertId();
		
		// update log file
		$log = new Log();
		$log->write("CHECKOUT [by ".$agent." (".$_SERVER['REMOTE_ADDR'].")] ".$this->last_id." ".$user." ".$this->titles->type($title)." - ".$this->titles->title($title));
		
		return true;
	}
	
	public function add_migrate($user,$title,$note,$building,$agent,$date_in,$date_out,$status,$term)
	{
		if ( $user != "" AND $term != "" AND $title != "" AND $building != "" AND $agent != "" )
		{
			$sql = "INSERT INTO records (user, term, title, note, building, agent, date_out, date_in, status) VALUES (";
			$sql = $sql."'".$user."',".$term.",'".$title."','".$note."','".$building."','".$agent."',".$date_out.",".$date_in.",'".$status."');";
			if ( !$this->db->exec($sql)) echo "Failed to create record.";			
		} else echo "Failed to create record, required fields cannot be blank.";
	}
	
	// remove an existing record
	public function remove($id)
	{
		$record = $this->record_show($id);
		
		$sql = "DELETE FROM records WHERE id = ".$id.";";
		if ( $this->db->exec($sql) != 1 ) return false;
		$this->last_id = $id;
		
		// update log file
		$log = new Log();
		$log->write("DELETE [by ".$_SERVER['REMOTE_USER']." (".$_SERVER['REMOTE_ADDR'].")] ".$this->last_id." ".$record["user"]." ".$record["type"]." - ".$record["title"]);
		
		return true;
	}
	
	// mark an existing record as returned
	public function checkin($id)
	{
		$record = $this->record_show($id);
		
		$sql = "UPDATE records SET status = 'in', date_in = ".time()." WHERE id = ".$id.";";
		if ( $this->db->exec($sql) != 1 ) return false;
		$this->last_id = $id;
		
		// update log file
		$log = new Log();
		$log->write("CHECKIN [by ".$_SERVER['REMOTE_USER']." (".$_SERVER['REMOTE_ADDR'].")] ".$this->last_id." ".$record["user"]." ".$record["type"]." - ".$record["title"]);
		
		return true;
	}
	
	// update an existing record
	public function update($id,$user,$title,$note,$building,$agent,$term = 7)
	{
		$sql = "UPDATE records SET user = '".$user."', term = ".$term.", title = ".$title.", note = '".$note."', ";
		$sql = $sql."building = '".$building."', agent = '".$agent."' WHERE id = ".$id.";";
		if ( $this->db->exec($sql) != 1 ) return false;
		$this->last_id = $id;
		
		// update log file
		$log = new Log();
		$log->write("UPDATE [by ".$_SERVER['REMOTE_USER']." (".$_SERVER['REMOTE_ADDR'].")] ".$this->last_id." ".$user." ".$this->titles->type($title)." - ".$this->titles->title($title));
		
		return true;		
	}
	
	// returns a single record
	public function record_show($id)
	{
		$sql = "SELECT * FROM records WHERE id = ".$id.";";
		$array = array();
		foreach ( $this->db->query($sql) as $row )
		{
			$array["id"] = $row["id"];
			$array["user"] = $row["user"];
			$array["term"] = $row["term"];
			$array["type"] = $this->titles->type($row["title"]);
			$array["title"] = $this->titles->title($row["title"]);
			$array["title_id"] = $row["title"];
			$array["note"] = $row["note"];
			$array["building"] = $row["building"];
			$array["agent"] = $row["agent"];
			$array["date_out"] = $row["date_out"];
			$array["date_in"] = $row["date_in"];
			$array["status"] = $row["status"];
		}
		return $array;
	}
	
	// returns a page of records based on a search
	public function record_list($search, $page = 1, $title = 0)
	{
		// prepare query
		$query = "SELECT * FROM records";
		$view = "";
		$user = "";
		if ( $search == "OUT-ALL" ) $view = " WHERE status = 'out' AND term != 0";
		if ( $search == "OUT-EECS" ) $view = " WHERE status = 'out' AND building = 'EECS' AND term != 0";
		if ( $search == "OUT-CSE" ) $view = " WHERE status = 'out' AND building = 'CSE' AND term != 0";
		if ( $search == "ALL-EECS" ) $view = " WHERE building = 'EECS' AND term != 0";
		if ( $search == "ALL-CSE" ) $view = " WHERE building = 'CSE' AND term != 0";
		if ( $search == "LONG" ) $view = " WHERE term = 0 AND status = 'out'";
		if ( $search == "LATE" ) $view = " WHERE date_out < (".time()."-(term*86400)) AND status = 'out' AND term != 0";
		if ( $search == "TITLE" ) $view = " WHERE title = ".$title;
		if ( $pos = strripos($search,"USER:") ) $view = " WHERE user = '".substr($search,$pos+5)."'";
				
		// setup pagination
		if ( $view == "" ) $sql = "SELECT count(*) FROM records;";
		else $sql = "SELECT count(*) FROM records".$view.";";
		foreach ( $this->db->query($sql) as $row ) $numrows = $row[0];
		$perpage = 50;
		$lastpage = ceil($numrows/$perpage);
		
		// validate page number
		if ( $page > $lastpage ) $page = $lastpage;
		if ( $page < 1 ) $page = 1;
		
		// build query
		$limit = "LIMIT ".($page-1)*$perpage.",".$perpage;
		$limit = "LIMIT ".$perpage." OFFSET ".($page-1)*$perpage;
		//$sql = $query.$view." ORDER BY date_out+(term*86400) DESC ".$limit.";";
		$sql = $query.$view." 
		ORDER BY ABS( 
			".time()."-date_out+(term*86400) 
		) 
		ASC ".$limit.";";

		
		$result = $this->db->query($sql);
		
		// prepare result
		$array = array();
		$array["num_pages"] = $lastpage;
		$array["curr_page"] = $page;
		$array["per_page"] = $perpage;
		$array["records"] = array();
		
		// fill array
		foreach ( $this->db->query($sql) as $row )
		{
			$array["records"][$row["id"]] = array();
			$array["records"][$row["id"]]["id"] = $row["id"];
			$array["records"][$row["id"]]["user"] = $row["user"];
			$array["records"][$row["id"]]["term"] = $row["term"];
			$array["records"][$row["id"]]["type"] = $this->titles->type($row["title"]);
			$array["records"][$row["id"]]["title"] = $this->titles->title($row["title"]);
			$array["records"][$row["id"]]["title_id"] = $row["title"];
			$array["records"][$row["id"]]["note"] = $row["note"];
			$array["records"][$row["id"]]["building"] = $row["building"];
			$array["records"][$row["id"]]["agent"] = $row["agent"];
			$array["records"][$row["id"]]["date_out"] = $row["date_out"];
			$array["records"][$row["id"]]["date_in"] = $row["date_in"];
			$array["records"][$row["id"]]["status"] = $row["status"];
			if ($this->titles->form_required($row["title"]) == 1) $array["records"][$row["id"]]["form_required"] = TRUE;
			else $array["records"][$row["id"]]["form_required"] = FALSE;
		}
		return $array;
	}
	
	// returns an array of all records matching a search
	public function record_search($building = "ALL",$type = "ALL",$period = "ALL", $status = "ALL", $user = "ALL")
	{
		$search = "";
		if ( $building == "ALL" ) $building = "";
		if ( $type == "ALL" ) $type = "";
		if ( $period == "ALL" ) $period = "";
		if ( $status == "ALL" ) $status = "";
		if ( $user == "ALL" ) $user = "";
		
		// check building
		if ( strlen($building) > 0 )
		{
			if ( $building == "EECS" ) $search = " WHERE building = 'EECS'";
			if ( $building == "CSE" ) $search = " WHERE building = 'CSE'";
		}
		
		// check period
		if ( strlen($period) > 0 )
		{
			if ( strlen($search) > 0 ) $search .= " AND ";
			else $search .= "WHERE ";
			if ( $period == "DAY" ) $search .= " date_out > ".strtotime("-1 day");
			if ( $period == "WEEK" ) $search .= " date_out > ".strtotime("-1 week");
			if ( $period == "MONTH" ) $search .= " date_out > ".strtotime("-1 month");
			if ( $period == "6MONTHS" ) $search .= " date_out > ".strtotime("-6 months");
			if ( $period == "YEAR" ) $search .= " date_out > ".strtotime("-1 year");
		}
		
		// check status
		if ( strlen($status) > 0 )
		{
			if ( strlen($search) > 0 ) $search .= " AND ";
			else $search .= "WHERE ";
			if ( $status == "OUT" ) $search .= " status = 'out'";
			if ( $status == "IN" ) $search .= " status = 'in'";
		}
		
		// check user
		if ( strlen($user) > 0 )
		{
			if ( strlen($search) > 0 ) $search .= " AND ";
			else $search .= "WHERE ";
			if ( $user != "" ) $search .= " user = '".$user."'";
		}
		
		// finalize query
		$sql = "SELECT * FROM records ".$search." ORDER BY date_out DESC;";
		
		// fill array
		$array = Array();
		foreach ( $this->db->query($sql) as $row )
		{
			if ( $type == $this->titles->type($row["title"]) OR $type == "" )
			{
				$array[$row["id"]] = array();
				$array[$row["id"]]["id"] = $row["id"];
				$array[$row["id"]]["user"] = $row["user"];
				$array[$row["id"]]["term"] = $row["term"];
				$array[$row["id"]]["title_id"] = $row["title"];
				$array[$row["id"]]["type"] = $this->titles->type($row["title"]);
				$array[$row["id"]]["title"] = $this->titles->title($row["title"]);
				$array[$row["id"]]["note"] = $row["note"];
				$array[$row["id"]]["building"] = $row["building"];
				$array[$row["id"]]["agent"] = $row["agent"];
				$array[$row["id"]]["date_out"] = $row["date_out"];
				$array[$row["id"]]["date_in"] = $row["date_in"];
				$array[$row["id"]]["status"] = $row["status"];
			}
		}
		return $array;

	}
	
	// return status of record
	public function status( $id )
	{
		$record = $this->record_show($id);
		if ( $record["status"] == "in" ) return "in";
		if ( $record["status"] == "out" ) 
		{
			if ( $record["term"] != 0 )
			{
				// way late
				if ( $record["date_out"] < (time()-(($record["term"]+7)*86400)) ) return "way";
				// late
				if ( $record["date_out"] < (time()-(($record["term"])*86400)) ) return "late";
			}
			// out
			return "out";
		}
	}
	
	public function spg520status($id)
	{
		$record = $this->record_show($id);
		if ( $this->titles->form_required($record["title_id"]) == 1 ) return TRUE;
		else return FALSE;
	}
	
	public function karma( $user )
	{
		$records = $this->record_search("ALL","ALL","ALL","ALL",$user);
		$in = $in_late = $out_late = $out_way = 0;
		foreach ( $records as $record ) 
		{
			if ( $record["term"] != 0 )
			{
				// returned on time
				if ( $record["status"] == "in" AND $record["date_in"] < strtotime("+".$record["term"]." days",$record["date_out"]) ) $in++;
				// returned late
				if ( $record["status"] == "in" AND $record["date_in"] > strtotime("+".$record["term"]." days",$record["date_out"]) ) $in_late++;
				// out and way late
				if ( $record["status"] == "out" AND $record["date_out"] < strtotime("-".($record["term"]+7)." days") ) $out_way++;
				// out and late
				if ( $record["status"] == "out" AND $record["date_out"] < strtotime("-".$record["term"]." days") ) $out_late++;
				
			}
		}
		$karma = 0;
		$out_late = $out_late - $out_way;
		$total = $in+$in_late+$out_late+$out_way;
		if ( $total == 0 ) $karma = 10;
		else $karma = (($in * 10) + ($in_late * 7) + ($out_late * 2) + ($out_way * 0))/$total;
		return round($karma,1);
	}
	
	public function holds( $user )
	{
		$count = 0;
		$records = $this->record_search("ALL","ALL","ALL","ALL",$user);
		foreach ($records as $record) {
			if ( $this->status($record["id"]) == "late" OR $this->status($record["id"]) == "way" )
				$count++;
		}
		if ( $count > 0 ) return true;
		else return false;
	}
	
	public function offenders()
	{
		$records = $this->record_search("ALL","ALL","ALL","ALL","ALL");
		$users = array();
		// go through records and find users
		foreach ( $records as $record ) {
			if ( isset($users[$record["user"]]) ) {
				$users[$record["user"]]["count"] += 1;
			}
			else $users[$record["user"]] = array("count" => 1);
		}
		$holds = array();
		// compute karma and count holds for each person
		foreach ( $users as $key => $row ) {
			$users[$key]["user"] = $key;
			$users[$key]["karma"] = $this->karma($key);
			if ( $users[$key]["holds"] = $this->holds($key) ) {
			
				$reason = "";
				$hold_count = 0;
				foreach ($records as $record) {
					if ( $record["user"] == $key AND ($this->status($record["id"]) == "late" OR $this->status($record["id"]) == "way") ) {
						$hold_count++;
						$days = ($this->day_difference($record["date_out"],time()))-$record["term"];
						$reason .= "<a href='records.php?action=edit&id=".$record["id"]."'>Item</a> $days days overdue. ";
					}
				}
				$users[$key]["hold_count"] = $hold_count;
				$users[$key]["hold_reason"] = $reason;
				$holds[$key] = $users[$key];
			}
		}
		$counts = $users;
		// sort the arrays
		usort($users,array("Checkout","array_sort_karma_callback"));
		usort($counts,array("Checkout","array_sort_count_callback"));
		usort($holds,array("Checkout","array_sort_karma_callback"));
		// return the arrays
		return array("users" => $users, "counts" => $counts, "holds" => $holds);	
	}
	
	private function array_sort_karma_callback($a,$b) 
	{
		if ( $a["karma"] == $b["karma"] ) return 0;
		return ($a["karma"] < $b["karma"]) ? -1 : 1;
	}
	
	private function array_sort_count_callback($a,$b)
	{
		if ( $a["count"] == $b["count"] ) return 0;
		return ($a["count"] > $b["count"]) ? -1 : 1;
	}
	
	private function day_difference($time_start,$time_end) {
		$day_start = intval(date("z",$time_start));
		$year_start = intval(date("Y",$time_start));
		$day_end = intval(date("z",$time_end));
		$year_end =  intval(date("Y",$time_end));
		if ( $year_start != $year_end ) {
			$year_diff = $year_end-$year_start;
			return ((365-$day_start)+$day_end+(($year_diff-1)*365));
		}
		return ($day_end-$day_start);
	}
}

//$checkout = new Checkout();
//$checkout->titles->add("eeeeee","ffffff");
//$checkout->add("mcolf",3,"foobar","EECS","self","short");

//echo "<br />";
//$array = $checkout->record_list("ALL");
//foreach( $array["records"] as $record ) echo $record["id"]." ".$record["type"]." - ".$record["title"]." (".$record["user"].", ".$record["status"].")<br />";
//echo "PAGE 2<br />";
//$array = $checkout->record_list("ALL",2);
//foreach( $array["records"] as $record ) echo $record["id"]." ".$record["type"]." - ".$record["title"]." (".$record["user"].", ".$record["status"].")<br />";
//echo "PAGE 3<br />";
//$array = $checkout->record_list("ALL",3);
//foreach( $array["records"] as $record ) echo $record["id"]." ".$record["type"]." - ".$record["title"]." (".$record["user"].", ".$record["status"].")<br />";

//echo "TITLES<br />";
//$titles = $checkout->titles->find();
//foreach ( $titles as $title ) echo $title["id"]." ".$title["type"]." - ".$title["title"]." (".$title["enable"].")<br />";

//echo "RECORD<br />";
//$checkout->update(1,"mcolf",2,"Im a note","CSE","self","short");
//$record = $checkout->record_show(1);
//$checkout->checkin(1);
//echo $record["id"]." ".$record["type"]." - ".$record["title"]." (".$record["user"].", ".$record["status"].")<br />";

//echo "USER<br />";
//$records = $checkout->record_search("USER:mcolf");
//foreach( $records as $record ) echo $record["id"]." ".$record["type"]." - ".$record["title"]." (".$record["user"].", ".$record["status"].")<br />";

//echo "STATS<br />";
//$checkout->record_search("ALL","ALL","ALL","OUT","ALL");
//$stats = new Statistics($checkout->record_search("ALL"));
//$stats->pie_title();
//$stats->pie_status();
//$stats->bar_day();
//$stats->bar_month();

//echo "<br />end";