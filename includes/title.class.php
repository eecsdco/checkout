<?php

class Title
{
	public $db;
	
	// constructor
	function __construct()
	{
		$this->connect();
	}
	
	// establish database and open connection
	private function connect()
	{
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
			$sql = "CREATE TABLE IF NOT EXISTS titles( ";
			$sql = $sql."id INTEGER PRIMARY KEY AUTOINCREMENT, ";
			$sql = $sql."enable INTEGER NOT NULL DEFAULT 1, ";
			$sql = $sql."type TEXT NOT NULL, ";
			$sql = $sql."title TEXT NOT NULL, ";
			$sql = $sql."model TEXT NOT NULL, ";
			$sql = $sql."cost TEXT NOT NULL, ";
			$sql = $sql."form_required INTEGER NOT NULL DEFAULT 0, ";
			$sql = $sql."notice TEXT )";
			$this->db->exec($sql);
		}
		else die( "Unable to open database." );
	}
	
	// add a new title
	public function add($type,$title,$model,$cost)
	{
		if ( $type != "" AND $title != "" )
		{
			$sql = "INSERT INTO titles (type, title, model, cost) VALUES ('".$type."', '".$title."', '".$model."', ".$cost.");";
			if ( !$this->db->exec($sql)) return false;
		} else return false;
		return true;
	}
	
	// remove an existing title
	public function remove($id)
	{
		$sql = "DELETE FROM titles WHERE id = ".$id.";";
		if ( $this->db->exec($sql) != 1 ) return false;
		return true;
	}
	
	// update an existing title
	public function update($id,$type,$title,$notice,$model,$cost,$enable,$form_required = "null")
	{
		if ( $type == "null" ) $type = $this->type($id);
		if ( $title == "null" ) $title = $this->title($id);
		if ( $notice == "null" ) $notice = $this->notice($id);
		if ( $model == "null" ) $model = $this->model($id);
		if ( $cost == "null" ) $cost = $this->cost($id);
		if ( $enable == "null" ) $enable = $this->enabled($id);
		$sql = "UPDATE titles SET type = '".$type."', title = '".$title."', model = '".$model."', cost = '".$cost."', form_required = '".$form_required."', enable = '".$enable."', notice = '".$notice."' WHERE id = ".$id.";";
		if ( $this->db->exec($sql) != 1 ) return false;
		return true;
	}
	
	// enable or disable an existing title
	public function enable($id,$flag)
	{
		return $this->update($id,"null","null","null","null","null","null",$flag);
	}
	
	public function update_form_required($id,$flag)
	{
		return $this->update($id,"null","null","null","null","null","null",$flag);
	}
	
	public function enabled($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["enable"];
	}
	
	// retrieve type text of an existing title
	public function type($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["type"];
	}
	
	// retrieve title text of an existing title
	public function title($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["title"];
	}
	
	// retrieve model text of an existing title
	public function model($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["model"];
	}
	
	// retrieve cost text of an existing title
	public function cost($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["cost"];
	}
	
	// retrieve form_required value of an existing title
	public function form_required($id)
	{
		$result = $this->find("id",$id);
		return $result[$id]["form_required"];
	}
	
	// retrieve notice text of an existing title
	public function notice($id)
	{
		$result = $this->find("id",$id);
		$notice = $result[$id]["notice"];
		if ( $notice == "" ) return false;
		else return $notice;
	}
	
	// retrieve array of titles based on search criteria
	public function find($search = "all", $id = 0)
	{
		$result = array();
		if ( $search == "enabled" ) $sql = "SELECT * FROM titles WHERE enable = 1 ORDER BY type ASC, title ASC;";
		else if ( $search == "disabled" ) $sql = "SELECT * FROM titles WHERE enable = 0 ORDER BY type ASC, title ASC;";
		else if ( $search == "id" ) $sql = "SELECT * FROM titles WHERE id = ".$id.";";
		else $sql = "SELECT * FROM titles ORDER BY type ASC, title ASC;";
		foreach( $this->db->query($sql) as $row )
		{
			$result[$row["id"]] = array("id" => $row["id"], "enable" => $row["enable"], "type" => $row["type"], "title" => $row["title"], "model" => $row["model"], "cost" => $row["cost"], "form_required" => $row["form_required"], "notice" => $row["notice"]);
		}
		return $result;
	}	
}

?>

