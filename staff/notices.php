<?php require "../layout/section_header.php"; ?>

<div id="content">

	<?php
	
	require "../includes/notice.class.php";
	$notices = new Notice();
	
	// update template file
	$type = $_REQUEST["type"];
	$contents = $_REQUEST["contents"];
	if ( $_REQUEST["restore"] != "" )
	{
		if ( $notices->update($type,$notices->get($type.".default")) )
			echo "<p class='success'>Template restored to default.</p>";
		else
			echo "<p class='error'>An error occured while restoring the template to default.</p>";
	}
	else if ( $type != "" AND $contents != "" )
	{
		if ( $notices->update($type,$contents) )
			echo "<p class='success'>Template updated sucessfully.</p>";
		else
			echo "<p class='error'>An error occured while updating the template.</p>";
	}
	?>

	<h3>Loan Start Notice</h3>
	<p>This notice is sent to users upon sucessfully checking out an item. (plain text only)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("start.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="start.template" /><br />
		<input type="submit" value="Update Loan Start Notice" class="f-submit" tabindex="2" />
		<a href="notices.php?type=start.template&restore=true">Restore Default</a>
	</form>
	
	<h3>Loan Update Notice</h3>
	<p>This notice is sent to users when a change is made to their loan record. (plain text only)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("update.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="update.template" /><br />
		<input type="submit" value="Update Loan Start Notice" class="f-submit" tabindex="2" />
		<a href="notices.php?type=update.template&restore=true">Restore Default</a>
	</form>
	
	<h3>Loan End Notice</h3>
	<p>This notice is sent to users upon sucessfully returning an item. (plain text only)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("end.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="end.template" /><br />
		<input type="submit" value="Update Loan End Notice" class="f-submit" tabindex="2" />
		<a href="notices.php?type=end.template&restore=true">Restore Default</a>
	</form>
	
	<h3>Overdue Notice</h3>
	<p>This notice is sent to users after their loan period has expired. (plain text only)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("overdue.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="overdue.template" /><br />
		<input type="submit" value="Update Overdue Notice" class="f-submit" tabindex="2" />
		<a href="notices.php?type=overdue.template&restore=true">Restore Default</a>
	</form>
	
	<h3>Way Overdue Notice</h3>
	<p>This notice is sent to users 7 days after their loan period has expired. (plain text only)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("evil.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="evil.template" /><br />
		<input type="submit" value="Update Way Overdue Notice" class="f-submit" tabindex="2" />
		<a href="notices.php?type=evil.template&restore=true">Restore Default</a>
	</form>
	
	<h3>Request for Removal of Property</h3>
	<p>This notice is sent to users 7 days after their loan period has expired. (html formatting)</p>
	<form action = "notices.php" method = "post">
		<textarea id="contents" name="contents" rows="15" cols="75"><?php echo $notices->get("removal.template"); ?></textarea>
		<input type="hidden" id="type" name="type" value="removal.template" /><br />
		<input type="submit" value="Update Request for Removeal of Property" class="f-submit" tabindex="2" />
		<a href="notices.php?type=removal.template&restore=true">Restore Default</a>
	</form>
	
	<br />
		
	<div id="footer">
		<p>Layout based on <a href="http://www.mollio.org">Mollio</a> created by <a href="http://www.daemon.com.au">Daemon Pty</a></p>
		<p>Managed by the EECS <a href="http://www.eecs.umich.edu/dco">Departmental Computing Organization</a></p>
	</div>
			
	</div>
		
		<div id="sidebar">

			<div class="featurebox">
			<h3>Notice Templates</h3>
			<p>This page allows you to customize the notice emails that users receive.</p>
			</div>
			
			<div class="featurebox">
			<h3>Variables</h3>
			<p>
			The following variables are available for use:<br /><br />
			<b>{{user}}</b><br />uniquename<br />
			<b>{{name}}</b><br />user name<br />
			<b>{{title}}</b><br />loaned item<br />
			<b>{{title_notice}}</b><br />title's notice<br />
			<b>{{term}}</b><br />loan term<br />
			<b>{{date_out}}</b><br />loan start date<br />
			<b>{{account_link}}</b><br />link to user's account<br />
			<b>{{karma}}</b><br />user's karma rating<br />
			<b>{{due_date}}</b><br />item's due date<br />
			<b>{{model}}</b><br />item's model type<br />
			<b>{{cost}}</b><br />item's cost<br />
			<br />
			If you would like additional variables added, talk to Matt.
			</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>