<?php require "../layout/section_header.php"; ?>

<div id="content">
		
	<?php
	echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";
	
	require "../includes/checkout.class.php";
	require "../includes/statistics.class.php";
	require "../includes/helpers.php";
	
	$checkout = new Checkout();
	
	// check for messages
	if ( $notice = stripslashes($_REQUEST["notice"]) ) {
		echo "<div class='noticebox'><h3>Confirm</h3><p>$notice</p></div>";
	}
	if ( $error = stripslashes($_REQUEST["error"])) {
		echo "<p class='error'>$error</p>";
	}
	if ( $message = stripslashes($_REQUEST["message"])) {
		echo "<p class='success'>$message</p>";
	}

	// determine user
	$user = $_REQUEST["user"];
	
	// ask for user to look up
	if ( $user == "" )
	{
		?>
		<form action = "user.php" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>Retrieve User Records</h3>
		
			<label for="user"><b><span class="req">* </span>Uniquename:</b>
				<input id="user" name="user" type="text" class="f-name" tabindex="1" /><br />
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
	// show user details
	else
	{
		// determine page number
		$page = $_REQUEST["page"];
		if ( $page == "" ) $page = 1;
		
		// retrieve all records for user
		$records = $checkout->record_search("ALL","ALL","ALL","ALL",$user);
		
		echo "<h2>Account Summary for ".ldap_query($user,"cn")."</h2>";
		if ( valid_umich($user) ) echo "<span class='success'>A valid UMICH account exists for this user.</span><br />";
		else echo "<span class='highlight'>A valid UMICH account does not exist for this user.</span><br />";
		if ( valid_eecs($user) ) echo "<span class='success'>A valid EECS account exists for this user.</span>";
		else echo "<span class='highlight'>A valid EECS account does not exist for this user.</span>";
		echo "<h4>Account Holds</h4>";

		$count = 0;
		foreach ($records as $record) {
			if ( $checkout->status($record["id"]) == "late" OR $checkout->status($record["id"]) == "way" )
				$count++;
		}
		if ( $count > 0 ) 
			echo "<p class='error'>There are a total of ".$count." holds on this account. No additional items may be checked out.</p>";
		else
			echo "<p class='success'>No holds exist on this account.</p>";
		?>
		
		<h4>Karma Rating</h4>
		<?php 
		$karma = $checkout->karma($user);
		if ( $karma > 9 ) echo "<p><big class='success'>".$karma."</big>/10</p>";
		else if ( $karma > 7 ) echo "<p><big class='highlight'>".$karma."</big>/10</p>";
		else if ( $karma >= 0 ) echo "<p><big class='error'>".$karma."</big>/10</p>";
		?>
		<p>Karma is a rating that predicts how likely a person is to return a loaned item on time. Higher is better.</p>
		
		<h4>Statistics</h4>
		<?php 
		$chart = $_REQUEST["chart"]; 
		if ( $chart == "" ) $chart = 1;
		?>		
		<form action = "user.php" method = "post">
			<select id="chart" name="chart" tabindex="2">
				<option <?php if ( $chart == 1 ) echo "selected='selected'"; ?> value="1">Status Pie Chart</option>
				<option <?php if ( $chart == 2 ) echo "selected='selected'"; ?> value="2">Title Pie Chart</option>				
				<option <?php if ( $chart == 3 ) echo "selected='selected'"; ?> value="3">Weeky Occurance Count</option>
				<option <?php if ( $chart == 4 ) echo "selected='selected'"; ?> value="4">Monthy Occurance Count</option>
			</select>
			<input type="hidden" id="user" name="user" value="<?php echo $user; ?>" />
			<input type="submit" value="Show" class="f-submit" tabindex="2" />
		</form>
		<?php		
		$stats = new Statistics($records);
		if ( $chart == 1 ) $stats->pie_status();
		if ( $chart == 2 ) $stats->pie_title();		
		if ( $chart == 3 ) $stats->bar_day();
		if ( $chart == 4 ) $stats->bar_month();	
		?>
		
		<h4>All User Records</h4>
		<table class='table1'>
		<tbody>
			<tr>
				<th>Title</th>
				<th>Borrower</th>				
				<th>Due Date</th>
				<th>Building</th>
				<th>Term</th>
				<th>Status</th>
				<th>Actions</th>
			</tr>
			
			<?php
			foreach( $records as $record )
			{
				echo "<tr>";
				echo "<th class='sub'>".$record["type"]." - ".$record["title"]."</th>";
				echo "<td>".$record["user"]."</td>";
				if ( $record["term"] == 0 ) echo "<td>none</td>";
				else echo "<td>".date("n-j-y",$record["date_out"]+($record["term"]*86400))."</td>";
				echo "<td>".$record["building"]."</td>";
				echo "<td>";
				if ( $record["term"] == 0 ) echo "Long Term";
				else echo  $record["term"]." Day";
				echo "</td>";
				echo "<td>";
				if ( $checkout->status($record["id"]) == "in" ) echo "<span class='success'>Returned</span>";
				if ( $checkout->status($record["id"]) == "out" ) echo "<span class='highlight'>Out</span>";
				if ( $checkout->status($record["id"]) == "late" ) echo "<span class='error'>Overdue</span>";
				if ( $checkout->status($record["id"]) == "way" ) echo "<span class='error'>Way Overdue</span>";
				echo "</td>";
				echo "<td>";
				if ( $record["status"] == "out"  ) echo "<a href='actions.php?destination=user.php&options=".urlencode("user=$user")."&action=return&id=".$record["id"]."'>Return</a> | ";
				echo "<a href='records.php?action=edit&id=".$record["id"]."'>Edit</a> | ";
				echo "<a href='viewlog.php?term=".$record["id"]."'>Logs</a>";
				echo "</td></tr>";
			}	
			?>
		
		</tbody>
		</table>
		<br />
		<a href="viewlog.php?term=<?=$user?>">View all log file entries for this user.</a>
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
			<h3>Account Summary</h3>
			<p>This page gives a quick summary of a user's checkout history, holds, and karma rating. It can also be used to check items in.</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>