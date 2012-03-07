<?php

// query user info from UMICH LDAP directory
function ldap_query($uid,$entry)
{
	$server = "ldap.itd.umich.edu";
	$port = 389;
	// check if ldap server is accessible
	if ( $fp = fsockopen($server,$port,$err,$errString,1) ) {
		// it's available
		fclose($fp);
	}
	else die("Unable to connect to LDAP server! $errString Please try again later.");
	
		
	$conn = ldap_connect($server,$port);
	if ($conn)
	{
		ldap_set_option($conn,LDAP_OPT_TIMELIMIT,5);
		if (ldap_bind($conn))
		{
			$dn = "ou=People,dc=umich,dc=edu";
			$filter = "uid=".$uid;
			$result=ldap_search($conn, $dn, $filter);
		    $entries = ldap_get_entries($conn, $result);
			//$result = $entries[0][$entry][0];
			// if entry is private, only 'mail' returns
			if ( !isset($entries[0][$entry][0]) and $entry == "cn" ) return ldap_query($uid,"mail");
			else return $entries[0][$entry][0];
		}
		else return false;
		ldap_close($conn);
	}
	else return false;
}

// check for a valid UMICH account
function valid_umich($user)
{
	if ( ldap_query($user,"mail") ) return true;
}

// check for a valid EECS account
function valid_eecs($user)
{
	// while alias file is broken, assume valid
	return TRUE;
	
	//$file = "/etc/mail/aliases";
	//$handle = fopen($file,"r");
	//if ( $handle )
	//{
	//	$found = false;
	//	while ( $line = fgets($handle) )
	//	{
	//		$sub = substr($line,0,strlen($user));
	//		if ( strcasecmp($user,$sub) == 0 ) $found = true;
	//	}
	//	fclose($handle);
	//}
	//return $found;
}

// check for a valid uniquename
function valid_uniquename($user)
{
	if ( valid_umich($user) ) return true;
	if ( valid_eecs($user) ) return true;
	// must not be valid!
	return false;
}

function show_dates($date_start,$admin = true,$selected = null,$spg520 = false) {
	// determine maximum selection, limit to 2 weeks for SPG 520.3 bound titles
	if ( $spg520 ) $max = 15;
	else $max = 366;
	// set default selection
	$default = 2;
	$day = date("N",strtotime("+$default days",$date_start));
	if ( $day > 5 ) {
		if ( $day == 6 ) $default = $default+2;
		if ( $day == 7 ) $default = $default+1;
	}
	// show form
	if ( $admin == false ) {
		echo "<input type='hidden' name='term' id='term' value='$default' />";
		echo "<select id='term_disable' disabled='disabled' name='term_disable' tabindex='2' style='font-family: monospace;'>";
	}
	else echo "<select id='term' name='term' tabindex='2' style='font-family: monospace;'>";
	$overflow = "";
	// loop for date entries
	for ($term=0; $term<$max; $term++) {
		$stamp = strtotime("+ $term days",$date_start);
		// prepare date string
		$day = sprintf("%10s",date("D",$stamp));
		$month = sprintf("%10s",date("M",$stamp));
		$date = sprintf("%10s",date("d, Y",$stamp));
		$text = $day.$month.$date;
		// set note
		$note = "";
		if ( $term%30 == 0 AND ($term/30) > 0) $note = "[".($term/30)." months]";
		if ( $term == 1 ) $note = "[1 day]";
		if ( $term == 2 ) $note = "[2 days]";
		if ( $term == 7 ) $note = "[7 days]";
		if ( $term == 14 ) $note = "[14 days] [SPG 520.3 max]";
		if ( $term == 30 ) $note = "[30 days]";
		// check for semester end dates
		$sem = "";
		if (date("n",$stamp) == 6 && date("j",$stamp) == 20) $sem = "[SP".date("Y",$stamp)." ends]";
		if (date("n",$stamp) == 8 && date("j",$stamp) == 20) $sem = "[SU".date("Y",$stamp)." ends]";
		if (date("n",$stamp) == 12 && date("j",$stamp) == 20) $sem = "[FA".date("Y",$stamp)." ends]";
		if (date("n",$stamp) == 4 && date("j",$stamp) == 20) $sem = "[WN".date("Y",$stamp)." ends]";
		if ( strlen($note) > 0 ) $note = $note." ".$sem;
		else $note = $sem;
		// weekdays (show entry)
		if ( date("N",$stamp) < 6 ) {
			// clear overflow
			if ( strlen($note) > 0 ) $note = " ".$note." ".$overflow;
			else $note = " ".$overflow;
			$overflow = "";
			// print entry
			if ( $term == $default ) {
				if ( $selected == null or $selected == $default ) echo "<option selected='selected' value='$term'>".$text.$note." [default]</option>";
				else echo "<option value='$term'>".$text.$note." [default]</option>";
			}
			else {
				// only admins are allowed non-default loan lengths
				if ($admin) {
					if ($term == 0) {
						if ($selected == 0) echo "<option selected='selected' value='0'>No due date (long term loan)</option>";
						else echo "<option value='0'>No due date (long term loan)</option>";
					}
					else {
						if ($selected == $term) echo "<option selected='selected' value='$term'>".$text.$note."</option>";
						else echo "<option value='$term'>".$text.$note."</option>";
					}
				}
			}
		}
		// weekends (skip entry)
		else {
			// push to overflow
			if ( strlen($overflow) > 0 ) $overflow = $overflow." ".$note;
			else $overflow = $note;
		}
	}
	echo "</select>";
}