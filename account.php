<?php require "layout/section_header.php"; ?>

<div id="content">
		
<?php
	echo "<script language=javascript>setTimeout(\"location.href='http://www.eecs.umich.edu/dco/tools/checkout/index.php'\",120000);</script>";
	
	require "includes/checkout.class.php";
	require "includes/statistics.class.php";
	require "includes/helpers.php";
	
	$checkout = new Checkout();

	$user = $_SERVER['REMOTE_USER'];
	
	// ask for user to look up
	if ( $user == "" )
	{
		?>
		<form action = "account.php" method = "post" class = "f-wrap-1">
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
		echo "<script language=javascript>setTimeout(\"location.href='http://www.eecs.umich.edu/dco/tools/checkout/index.php'\",120000);</script>";
		
		// determine page number
		$page = $_REQUEST["page"];
		if ( $page == "" ) $page = 1;
		
		// retrieve all records for user
		$records = $checkout->record_search("ALL","ALL","ALL","ALL",$user);
		
		if ( strlen($name = ldap_query($user,"cn")) > 2 ) echo "<h2>Account Summary for $name</h2>";
		else echo "<h2>Account Summary for $user</h2>";
		echo "<h4>Account Holds</h4>";

		$count = 0;
		foreach ($records as $record) {
			if ( $checkout->status($record["id"]) == "late" OR $checkout->status($record["id"]) == "way" )
				$count++;
		}
		if ( $count > 0 ) 
			echo "<p><span class='error'>There are a total of ".$count." holds on your account.<br />No additional items may be checked out.<br />This hold will be removed when all overdue items are returned.</span></p>";
		else
			echo "<p class='success'>No holds exist on your account.</p>";
		?>
		
		<h4>Karma Rating</h4>
		<?php 
		$karma = $checkout->karma($user);
		if ( $karma > 9 ) echo "<p><big class='success'>".$karma."</big>/10</p>";
		else if ( $karma > 7 ) echo "<p><big class='highlight'>".$karma."</big>/10</p>";
		else if ( $karma >= 0 ) echo "<p><big class='error'>".$karma."</big>/10</p>";
		?>
		<p>Your karma rating is based on your past checkout history. Higher is better.</p>
		
		<h4>Statistics</h4>
		<?php 
		$chart = $_REQUEST["chart"]; 
		if ( $chart == "" ) $chart = 1;
		?>		
		<form action = "account.php" method = "post">
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
				echo "</tr>";
			}	
			?>
		
		</tbody>
		</table>
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
			<h3>My Account</h3>
			<p>This page gives an overview of your account including any account holds, karma rating, statistics, and your checkout history.</p>
			</div>
			
		</div>
	
	
<?php require "layout/footer.php"; ?>