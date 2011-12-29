<?php

class Statistics
{
	private $data;
	
	// constructor
	function __construct($array)
	{
		$this->data = $array;
	}
	
	function pie_title()
	{
		// count number of records for each title
		$total = 0;
		$counts = array_fill(0,50,array('id' => 0, 'count' => 0, 'title' => ""));
		foreach( $this->data as $record )
		{
			$counts[$record["title_id"]]["title"] = $record["type"]." - ".$record["title"];
			$counts[$record["title_id"]]["id"] = $record["title_id"];
			$counts[$record["title_id"]]["count"]++;
			$total++;
		}
		// sort array
		foreach ( $counts as $key => $row )
		{
			$id[$key] = $row["id"];
			$count[$key] = $row["count"];
		}
		array_multisort($count, SORT_DESC, $id, SORT_ASC, $counts);
		// build chart link
		$percents = "";
		$text = "";
		$cutoff = 9;
		// show top 9 titles
		for( $row = 0; $row < $cutoff AND $counts[$row]["count"] > 0; $row++ )
		{
			if ( $percents != "" ) $percents = $percents.",";
			$percents = $percents.ceil(($counts[$row]["count"]/$total)*100);
			
			if ( $text != "" ) $text = $text."|";
			$text = $text.$counts[$row]["title"]." (".$counts[$row]["count"].")";
		}
		// show remaining titles as 'other'
		$remainder = 0;
		for ( $row = $cutoff; $row < count($counts) AND $counts[$row]["count"] > 0; $row++ )
		{
			$remainder = $remainder + $counts[$row]["count"];
		}
		if ( $remainder != 0 )
		{
			$percents = $percents.",".ceil(($remainder/$total)*100);
			$text = $text."|Other (".$remainder.")";
		}
		echo "<img src='http://chart.apis.google.com/chart?chs=700x150&chd=t:".$percents."&cht=p&chl=".$text."' alt='Sample chart' />";
	}
	
	function pie_status()
	{
		$in = $in_late = $out = $out_late = 0;
		foreach ( $this->data as $record )
		{
			if ( $record["status"] == "in" )
			{
				// returned late
				if ( $record["date_in"] > strtotime("+".$record["term"]." days",$record["date_out"]) ) $in_late++;
				// returned on time
				else $in++;
			}			
			if ( $record["status"] == "out" )
			{
				// overdue
				if ( $record["term"] != 0 AND $record["date_out"] < strtotime("-".$record["term"]." days") ) $out_late++;
				// out
				else $out++;
			}
		}
		// build chart link
		$total = $in + $in_late + $out + $out_late;
		$text = $percents = "";
		if ( $in )
		{
			$text = $text."Returned on Time (".$in.")";
			$percents = $percents.ceil(($in/$total)*100);
		}
		if ( $in_late )
		{
			if ( $text != "" ) $text = $text."|";
			if ( $percents != "" ) $percents = $percents.",";
			$text = $text."Returned Late (".$in_late.")";
			$percents = $percents.ceil(($in_late/$total)*100);
		}
		if ( $out )
		{
			if ( $text != "" ) $text = $text."|";
			if ( $percents != "" ) $percents = $percents.",";
			$text = $text."Checked Out (".$out.")";
			$percents = $percents.ceil(($out/$total)*100);
		}
		if ( $out_late )
		{
			if ( $text != "" ) $text = $text."|";
			if ( $percents != "" ) $percents = $percents.",";
			$text = $text."Overdue (".$out_late.")";
			$percents = $percents.ceil(($out_late/$total)*100);
		}
		echo "<img src='http://chart.apis.google.com/chart?chs=700x150&chd=t:".$percents."&cht=p&chl=".$text."' alt='Sample chart' />";
	}
	
	function bar_day()
	{
		$csun = $cmon = $ctue = $cwed = $cthu = $cfri = $csat = 0;
		$esun = $emon = $etue = $ewed = $ethu = $efri = $esat = 0;
		foreach ( $this->data as $record )
		{
			if ( date("w",$record["date_out"]) == "0" && $record["building"] == "CSE" ) $csun++;
			if ( date("w",$record["date_out"]) == "0" && $record["building"] == "EECS" ) $esun++;
			if ( date("w",$record["date_out"]) == "1" && $record["building"] == "CSE" ) $cmon++;
			if ( date("w",$record["date_out"]) == "1" && $record["building"] == "EECS" ) $emon++;
			if ( date("w",$record["date_out"]) == "2" && $record["building"] == "CSE" ) $ctue++;
			if ( date("w",$record["date_out"]) == "2" && $record["building"] == "EECS" ) $etue++;
			if ( date("w",$record["date_out"]) == "3" && $record["building"] == "CSE" ) $cwed++;
			if ( date("w",$record["date_out"]) == "3" && $record["building"] == "EECS" ) $ewed++;
			if ( date("w",$record["date_out"]) == "4" && $record["building"] == "CSE" ) $cthu++;
			if ( date("w",$record["date_out"]) == "4" && $record["building"] == "EECS" ) $ethu++;
			if ( date("w",$record["date_out"]) == "5" && $record["building"] == "CSE" ) $cfri++;
			if ( date("w",$record["date_out"]) == "5" && $record["building"] == "EECS" ) $efri++;
			if ( date("w",$record["date_out"]) == "6" && $record["building"] == "CSE" ) $csat++;
			if ( date("w",$record["date_out"]) == "6" && $record["building"] == "EECS" ) $esat++;
		}
		if ( !$total = $csun + $cmon + $ctue + $cwed + $cthu + $cfri + $csat + $esun + $emon + $etue + $ewed + $ethu + $efri + $esat ) $total = 1;
		$max = max($csun,$cmon,$ctue,$cwed,$cthu,$cfri,$csat,$esun,$emon,$etue,$ewed,$ethu,$efri,$esat);
		$max = ($max/$total)+0.02;
		$cdata = ($csun/$total).",".($cmon/$total).",".($ctue/$total).",".($cwed/$total).",".($cthu/$total).",".($cfri/$total).",".($csat/$total);
		$edata = ($esun/$total).",".($emon/$total).",".($etue/$total).",".($ewed/$total).",".($ethu/$total).",".($efri/$total).",".($esat/$total);
		$labels = "chxt=x&chxl=0:|Su|Mo|Tu|We|Th|Fr|Sa|";
		echo "<img src='http://chart.apis.google.com/chart?cht=bvg&chs=600x180&chdl=CSE|EECS&chco=4D89F9,C6D9FD&chd=t:".$cdata."|".$edata."&".$labels."&chbh=a&chds=0,1&chm=N*p0*,000000,0,-1,11|N*p0*,000000,1,-1,11&chds=0,".$max."' alt='Sample Chart' />";
		
	}
	
	function bar_month()
	{
		$cjan = $cfeb = $cmar = $capr = $cmay = $cjun = $cjul = $caug = $csep = $coct = $cnov = $cdec = 0;
		$ejan = $efeb = $emar = $eapr = $emay = $ejun = $ejul = $eaug = $esep = $eoct = $enov = $edec = 0;
		foreach ( $this->data as $record )
		{
			if ( date("n",$record["date_out"]) == "1" && $record["building"] == "CSE" ) $cjan++;
			if ( date("n",$record["date_out"]) == "1" && $record["building"] == "EECS" ) $ejan++;
			if ( date("n",$record["date_out"]) == "2" && $record["building"] == "CSE" ) $cfeb++;
			if ( date("n",$record["date_out"]) == "2" && $record["building"] == "EECS" ) $efeb++;
			if ( date("n",$record["date_out"]) == "3" && $record["building"] == "CSE" ) $cmar++;
			if ( date("n",$record["date_out"]) == "3" && $record["building"] == "EECS" ) $emar++;
			if ( date("n",$record["date_out"]) == "4" && $record["building"] == "CSE" ) $capr++;
			if ( date("n",$record["date_out"]) == "4" && $record["building"] == "EECS" ) $eapr++;
			if ( date("n",$record["date_out"]) == "5" && $record["building"] == "CSE" ) $cmay++;
			if ( date("n",$record["date_out"]) == "5" && $record["building"] == "EECS" ) $emay++;
			if ( date("n",$record["date_out"]) == "6" && $record["building"] == "CSE" ) $cjun++;
			if ( date("n",$record["date_out"]) == "6" && $record["building"] == "EECS" ) $ejun++;
			if ( date("n",$record["date_out"]) == "7" && $record["building"] == "CSE" ) $cjul++;
			if ( date("n",$record["date_out"]) == "7" && $record["building"] == "EECS" ) $ejul++;
			if ( date("n",$record["date_out"]) == "8" && $record["building"] == "CSE" ) $caug++;
			if ( date("n",$record["date_out"]) == "8" && $record["building"] == "EECS" ) $eaug++;
			if ( date("n",$record["date_out"]) == "9" && $record["building"] == "CSE" ) $csep++;
			if ( date("n",$record["date_out"]) == "9" && $record["building"] == "EECS" ) $esep++;
			if ( date("n",$record["date_out"]) == "10" && $record["building"] == "CSE" ) $coct++;
			if ( date("n",$record["date_out"]) == "10" && $record["building"] == "EECS" ) $eoct++;
			if ( date("n",$record["date_out"]) == "11" && $record["building"] == "CSE" ) $cnov++;
			if ( date("n",$record["date_out"]) == "11" && $record["building"] == "EECS" ) $enov++;
			if ( date("n",$record["date_out"]) == "12" && $record["building"] == "CSE" ) $cdec++;
			if ( date("n",$record["date_out"]) == "12" && $record["building"] == "EECS" ) $edec++;
		}
		if ( !$total = $cfeb+$cmar+$capr+$cmay+$cjun+$cjul+$caug+$csep+$coct+$cnov+$cdec+$efeb+$emar+$eapr+$emay+$ejun+$ejul+$eaug+$esep+$eoct+$enov+$edec) $total = 1;
		$max = max($cjan,$cfeb,$cmar,$capr,$cmay,$cjun,$cjul,$caug,$csep,$coct,$cnov,$cdec,$ejan,$efeb,$emar,$eapr,$emay,$ejun,$ejul,$eaug,$esep,$eoct,$enov,$edec);
		$max = ($max/$total)+0.02;
		$cdata = ($cjan/$total).",".($cfeb/$total).",".($cmar/$total).",".($capr/$total).",".($cmay/$total).",".($cjun/$total).",".($cjul/$total).",".($caug/$total).",".($csep/$total).",".($coct/$total).",".($cnov/$total).",".($cdec/$total);
		$edata = ($ejan/$total).",".($efeb/$total).",".($emar/$total).",".($eapr/$total).",".($emay/$total).",".($ejun/$total).",".($ejul/$total).",".($eaug/$total).",".($esep/$total).",".($eoct/$total).",".($enov/$total).",".($edec/$total);
		$labels = "chxt=x&chxl=0:|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec|";
		echo "<img src='http://chart.apis.google.com/chart?cht=bvg&chs=600x180&chdl=CSE|EECS&chco=4D89F9,C6D9FD&chd=t:".$cdata."|".$edata."&".$labels."&chbh=a&chds=0,1&chm=N*p0*,000000,0,-1,11|N*p0*,000000,1,-1,11&chds=0,".$max."' alt='Sample Chart' />";
		
	}
	
	// line charts of title counts for each week (for each title in data set)
	function line_titles()
	{
		// count number of records for each title
		$total = 0;
		$counts = array_fill(0,50,array('id' => 0, 'count' => 0, 'title' => ""));
		foreach( $this->data as $record )
		{
			$counts[$record["title_id"]]["title"] = $record["type"]." - ".$record["title"];
			$counts[$record["title_id"]]["id"] = $record["title_id"];
			$counts[$record["title_id"]]["count"]++;
			$total++;
		}
		// sort array
		foreach ( $counts as $key => $row )
		{
			$id[$key] = $row["id"];
			$count[$key] = $row["count"];
		}
		array_multisort($count, SORT_DESC, $id, SORT_ASC, $counts);
		// for each title (startign at highest)
		$data_str = "";
		$label_str = "";
		$max = 0;
		$count = 0;
		foreach ( $counts as $title )
		{
			// only process titles with counts greater than zero
			if ( $title["count"] > 0 AND $count++ < 5 )
			{
				// add label to string
				if ( $label_str != "" ) $label_str = $label_str."|";
				$label_str = $label_str.$title["title"];
				// add title data to string (for each week)
				$title_data = "";
				for ( $offset = 24; $offset > 0; $offset-- )
				{
					$week_count = 0;
					// establish start and end timestamps
					$start = strtotime("-".$offset." weeks");
					$end = strtotime("-".($offset-1)." weeks");
					// find matching titles
					foreach($this->data as $record)
					{
						if ( $record["title_id"] == $title["id"] AND $record["date_out"] > $start AND $record["date_out"] <= $end )
						{
							$week_count++;
						}
					}
					// add count to data string
					if ( $title_data != "" ) $title_data = $title_data.",";
					$title_data = $title_data.$week_count;
					// update max range
					if ( $week_count > $max ) $max = $week_count;
				}
				// add title data to overall data string
				if ( $data_str != "" ) $data_str = $data_str."|";
				$data_str = $data_str.$title_data;
			}
		}
		$chart_url = "http://chart.apis.google.com/chart?cht=lc&chs=600x200&chd=t:".$data_str."&chdl=".$label_str."&chds=0,".$max."&chxt=y&chxl=0:|0|".$max."&chco=FF0000,FF8040,00FF00,0000FF,800080";
		echo "<img src='".$chart_url."' alt='Sample Chart' />";
	}

}

?>
