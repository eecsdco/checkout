<?php require "../layout/section_header.php"; ?>

<div id="content">
		
	<?php
	echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";
	
	require "../includes/checkout.class.php";
	require "../includes/statistics.class.php";
	require "../includes/helpers.php";
	
	$checkout = new Checkout();

	// get search term
	$term = $_REQUEST["term"];
	if ( $term != "" )
	{
		require_once "../includes/log.class.php";
		$term = $_REQUEST["term"];
		$log = new Log();
		$results = $log->search($term);

		?>
		<a href="viewlog.php">Seach Again</a><br /><br />
		<p>Log file entries are in the following format:<br />
		DAY MONTH DATE, YEAR TIME <b>ACTION</b> [by CHECKOUT-USER (IP)] CHECKOUT-ID END-USER CHECKOUT-ITEM</p>
		<table class='table1'>
		<tbody>
			<tr>
				<th>Log file matches for search term '<?=$term?>'</th>
			</tr>
			
			<?php
			foreach( $results as $result )
			{
				// bold action
				$result = str_replace("CHECKOUT","<b style='color:orange;'>CHECKOUT</b>",$result);
				$result = str_replace("CHECKIN","<b style='color:green;'>CHECKIN</b>",$result);
				$result = str_replace("EVIL-REMINDER","<b style='color:red;'>EVIL-REMINDER</b>",$result);
				$result = str_replace("REMINDER","<b style='color:red;'>REMINDER</b>",$result);
				$result = str_replace("DELETE","<b style='color:purple;'>DELETE</b>",$result);
				$result = str_replace("UPDATE","<b style='color:blue;'>UPDATE</b>",$result);
				
				// format time
				$ISO8601 = substr($result,0,25);
				$result = substr($result,25);
				$timestamp = strtotime($ISO8601);
				$datetime = date("D M d, Y h:i A",$timestamp);
				$result = $datetime.$result;
				
				// format brackets
				$spos = stripos($result,"[");
				$epos = stripos($result,"]");
				if ( $spos && $epos ) {
					$sub = substr($result,$spos,($epos-$spos+1));
					$result = str_replace($sub,"<span style='color:gray;'>".$sub."</span>",$result);
				}	
				
				// print line
				echo "<tr>";
				echo "<td>".$result."</td>";
				echo "</tr>";
			}	
			?>
		
		</tbody>
		</table>
		<br />
		
		<?php
	}
	else
	{
		// show search form
		?>
		
		<form action = "viewlog.php?action=" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>Search Log File</h3>
			<p>All actions taken in the DCO checkout system are logged to a log file.<br />This 
form 
allows you to search that file in a simple way (it's just a grep, don't get too excited).<br />You can also <a 
href="viewlog.php?term=ALL">view the 
entire</a> log file (this could take a moment).</p>
		
			<label for="term"><b><span class="req">* </span>Search Term:</b>
				<input id="term" name="term" type="text" class="f-name" tabindex="1" /><br />
			</label>
			
			<div class="f-submit-wrap">
				<input type="submit" value="Search" class="f-submit" tabindex="2" /><br />
			</div>
	
			</fieldset> 
		</form>
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		<br />
		
		<?php
	}
	
	?>
	
	
	
	<br />
	<br />
	<br />

		
	<div id="footer">
		<p>Layout based on <a href="http://www.mollio.org">Mollio</a> created by <a href="http://www.daemon.com.au">Daemon Pty</a></p>
		<p>Managed by the EECS <a href="http://www.eecs.umich.edu/dco">Departmental Computing Organization</a></p>
	</div>
			
	</div>
		
		<div id="sidebar">

			<div class="featurebox">
			<h3>Log Viewer</h3>
			<p>This page allows you to view the logs for a particular user or checkout record.</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>
