<?php require "../layout/section_header.php"; ?>

<div id="content">
		
	<?php
	echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";
	
	require "../includes/file.class.php";
	$files = new File();
	
	// update template file
	$type = $_REQUEST["type"];
	$contents = $_REQUEST["contents"];
	if ( $type != "" AND $contents != "" )
	{
		if ( $files->update($type,$contents) )
			echo "<p class='success'>Access list updated sucessfully.</p>";
		else
			echo "<p class='error'>An error occured while updating the access list.</p>";
	}
	?>
	
	<h3>Self Checkout IP List</h3>
	<p>This is the list of IP addresses approved to access the self checkout page. One entry per line formatted as follows: XXX.XXX.XXX.XXX #Note</p>
	<form action = "access.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $files->get("includes/checkout.access"); ?></textarea>
		<input type="hidden" id="type" name="type" value="includes/checkout.access" /><br />
		<input type="submit" value="Update Access List" class="f-submit" tabindex="2" />
	</form>
	
	<div id="footer">
		<p>Layout based on <a href="http://www.mollio.org">Mollio</a> created by <a href="http://www.daemon.com.au">Daemon Pty</a></p>
		<p>Managed by the EECS <a href="http://www.eecs.umich.edu/dco">Departmental Computing Organization</a></p>
	</div>
			
	</div>
		
		<div id="sidebar">

			<div class="featurebox">
			<h3>Access Lists</h3>
			<p>This page allows you to limit access to some pages. Be careful! You could accidentally lock yourself or others out.</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>