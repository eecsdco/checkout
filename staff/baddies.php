<?php require "../layout/section_header.php"; ?>

<div id="content">
		
	<?php
	//echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";
	
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

	?>
		
		<pre>
		<?php
		
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		
		$arrays = $checkout->offenders();
		$users = $arrays["users"];
		$counts = $arrays["counts"];
		$holds = $arrays["holds"];
		
		?>
		</pre>
	

	<h3>Active Holds</h3>
	
	<table class='table1'>
		<tbody>
			<tr>
				<th>User</th>
				<th>Number of Holds</th>
				<th>Reason for Hold</th>
			</tr>
			<?php 
			foreach ( $holds as $user ) { 
				if ( $user["user"] != "") {
					?>
					<tr>
						<th class='sub'><a href="user.php?user=<?=$user["user"]?>"><?=$user["user"]?></a></th>
						<td><?=$user["hold_count"]?></td>
						<td><?=$user["hold_reason"]?></td>
					</tr>
					<?php 
				} 
			} 
			?>
		</tbody>
	</table>	
	
	<h3>Top 20: Most Items Checked Out</h3>
	<p>This table shows the all-time top users of the checkout system.</p>
	
	<table class='table1'>
		<tbody>
			<tr>
				<th>Rank</th>
				<th>User</th>
				<th>Items Checked Out</th>				
			</tr>
			<?php 
			$count = 0;
			foreach ( $counts as $user ) { 
				if ( $user["user"] != "" AND $count < 20 ) {
					$count++;
					?>
					<tr>
						<th class='sub'><?=$count?></a>
						<td><a href="user.php?user=<?=$user["user"]?>"><?=$user["user"]?></a></td>
						<td><?=$user["count"]?></td>
					</tr>
					<?php 
				} 
			} 
			?>
		</tbody>
	</table>	
	
	<h3>Top 20: Worst Karma Rating</h3>
	<p>Karma is a rating that predicts how likely a person is to return a loaned item on time. Higher is better.</p>
	
	<table class='table1'>
		<tbody>
			<tr>
				<th>Rank</th>
				<th>User</th>
				<th>Karma Rating</th>				
			</tr>
			<?php 
			$count = 0;
			foreach ( $users as $user ) { 
				if ( $user["user"] != "" AND $count < 20 ) {
					$count++;
					?>
					<tr>
						<th class='sub'><?=$count?></a>
						<td><a href="user.php?user=<?=$user["user"]?>"><?=$user["user"]?></a></td>
						<td><?php echo $user["karma"]; if ( $user["karma"] == 10 ) echo " (perfect)"; ?></td>
					</tr>
					<?php 
				} 
			} 
			?>
		</tbody>
	</table>	
		
		
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
			<h3>Top Users</h3>
			<p>This page gives a quick summary of the top checkout users.</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>