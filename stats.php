<?php require "layout/section_header.php"; ?>

<div id="content">
		
	<?php 
	echo "<script language=javascript>setTimeout(\"location.href='http://www.eecs.umich.edu/dco/tools/checkout/index.php'\",120000);</script>";
	
	require "includes/checkout.class.php";
	require "includes/statistics.class.php";
	$checkout = new Checkout();
	
	$view = $_REQUEST["view"]; 
	if ( $view == "" ) $view = 2;
	?>		
	<form action = "stats.php" method = "post">
		<select id="building" name="building" tabindex="1">
			<option <?php if ( $_REQUEST["building"] == "ALL" ) echo "selected='selected'"; ?> value="ALL">All Buildings</option>
			<option <?php if ( $_REQUEST["building"] == "CSE" ) echo "selected='selected'"; ?> value="CSE">CSE Building</option>
			<option <?php if ( $_REQUEST["building"] == "EECS" ) echo "selected='selected'"; ?> value="EECS">EECS Building</option>
		</select>
		<select id="period" name="period" tabindex="2">
			<option <?php if ( $_REQUEST["period"] == "ALL" ) echo "selected='selected'"; ?> value="ALL">All Time</option>
			<option <?php if ( $_REQUEST["period"] == "DAY" ) echo "selected='selected'"; ?> value="DAY">Past Day</option>
			<option <?php if ( $_REQUEST["period"] == "WEEK" ) echo "selected='selected'"; ?> value="WEEK">Past Week</option>
			<option <?php if ( $_REQUEST["period"] == "MONTH" ) echo "selected='selected'"; ?> value="MONTH">Past Month</option>
			<option <?php if ( $_REQUEST["period"] == "6MONTHS" ) echo "selected='selected'"; ?> value="6MONTHS">Past 6 Months</option>
			<option <?php if ( $_REQUEST["period"] == "YEAR" ) echo "selected='selected'"; ?> value="YEAR">Past Year</option>
		</select>
		<select id="type" name="type" tabindex="3">
			<option <?php if ( $_REQUEST["type"] == "ALL" ) echo "selected='selected'"; ?> value="ALL">All Types</option>
			<option <?php if ( $_REQUEST["type"] == "Accessory" ) echo "selected='selected'"; ?> value="Accessory">Accessory</option>
			<option <?php if ( $_REQUEST["type"] == "Computer" ) echo "selected='selected'"; ?> value="Computer">Computer</option>
			<option <?php if ( $_REQUEST["type"] == "Monitor" ) echo "selected='selected'"; ?> value="Monitor">Monitor</option>
			<option <?php if ( $_REQUEST["type"] == "PC Parts" ) echo "selected='selected'"; ?> value="PC Parts">PC Parts</option>
			<option <?php if ( $_REQUEST["type"] == "Software" ) echo "selected='selected'"; ?> value="Software">Software</option>
			<option <?php if ( $_REQUEST["type"] == "Tools" ) echo "selected='selected'"; ?> value="Tools">Tools</option>
		</select>
		<select id="view" name="view" tabindex="2">
			<option <?php if ( $view == 1 ) echo "selected='selected'"; ?> value="1">Chart: Status</option>
			<option <?php if ( $view == 2 ) echo "selected='selected'"; ?> value="2">Chart: Titles</option>				
			<option <?php if ( $view == 3 ) echo "selected='selected'"; ?> value="3">Chart: Daily Counts</option>
			<option <?php if ( $view == 4 ) echo "selected='selected'"; ?> value="4">Chart: Monthly Counts</option>
			<option <?php if ( $view == 5 ) echo "selected='selected'"; ?> value="5">Chart: Title Trends</option>
		</select>
		<input type="hidden" id="user" name="user" value="<?php echo $user; ?>" />
		<input type="submit" value="Show" class="f-submit" tabindex="2" />
	</form>
	<?php	
	$records = $checkout->record_search($_REQUEST["building"],$_REQUEST["type"],$_REQUEST["period"]);
	$stats = new Statistics($records);
	if ( $view == 1 ) $stats->pie_status();
	if ( $view == 2 ) $stats->pie_title();		
	if ( $view == 3 ) $stats->bar_day();
	if ( $view == 4 ) $stats->bar_month();	
	if ( $view == 5 ) {
		echo "<p>This one is a work in progress...</p>";
		$stats->line_titles();
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
			<h3>Statistics</h3>
			<p>This page can be used to construct a custom set of statistics based on your search paremeters.</p>
			</div>
			
		</div>
	
	
<?php require "layout/footer.php"; ?>