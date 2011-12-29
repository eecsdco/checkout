<?php require "../layout/section_header.php"; ?>

<div id="content">

	<script type="text/javascript">
		function deleteParentElement(n){
			n.parentNode.parentNode.removeChild(n.parentNode);
		}
	</script>
	
	<?php
	//echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";
	
	require "../includes/checkout.class.php";
	require "../includes/statistics.class.php";
	require "../includes/notice.class.php";
	require "../includes/helpers.php"; 
	
	$checkout = new Checkout();
	
	// check for messages
	if ( isset($_REQUEST["notice"])  AND $notice = stripslashes($_REQUEST["notice"]) ) {
		echo "<div id='noticebox' class='noticebox'><h3>Notice</h3><p>$notice</p></div>";
	}
	if ( isset($_REQUEST["error"])  AND $error = stripslashes($_REQUEST["error"])) {
		echo "<p class='error'>$error</p>";
	}
	if ( isset($_REQUEST["message"])  AND $message = stripslashes($_REQUEST["message"])) {
		echo "<p class='success'>$message</p>";
	}
	
	// determine action
	if ( isset($_REQUEST["action"]) ) $action = $_REQUEST["action"];
	else $action = NULL;
	
	// show edit form
	if ( $action == "edit" OR $action == "remove" )
	{
		$record = $checkout->record_show($_REQUEST["id"]);
		
		?>
		
		<form action = "actions.php?destination=records.php&action=update&id=<?php echo $record["id"]; ?>" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>Update Record</h3>
			<p><small>This record can be <a href="actions.php?destination=records.php&action=remove&id=<?php echo $record["id"]; ?>">removed</a>. Please note that this is not advised.<br />The form below should be used for correcting mistakes only. To check an item in, click the 'return' link.<br /></small></p>

			<?php
			
			if ( $checkout->spg520status($record["id"]) ) {
				echo "<p><b>Note:</b> This record is for an item that has been classified as high value. Loans of this type have a maximum term of 14 days.</p>";
			}

			// set lock status
			if ( $checkout->status($record["id"]) == "in" ) {
				$lock = "disabled='disabled'";
				echo "<p><small>Please note, most fields cannot be modified after the item has been returned.</small></p>";
			}
			else echo $lock="";
			
			?>
			
			<label for="user"><b><span class="req">* </span>Uniquename:</b>
				<input id="user" <?=$lock?> name="user" type="text" class="f-name" value="<?php echo $record["user"]; ?>" tabindex="1" /><br />
			</label>
			
			<?php
			
			function day_diff($time_start,$time_end) {
				$day_start = intval(date("z",$time_start));
				$year_start = intval(date("Y",$time_start));
				$day_end = intval(date("z",$time_end));
				$year_end =  intval(date("Y",$time_end));
				if ( $year_start != $year_end ) {
					$year_diff = $year_end-$year_start;
					return ((365-$day_start)+$day_end+(($year_diff-1)*365));
				}
				return ($day_end-$day_start);
			}

			if ( $checkout->status($record["id"])  == "in" ) {
				if ( $record["date_in"] > ($record["date_out"]+($record["term"]*86400)) ) {
					$days = (day_diff($record["date_out"],$record["date_in"])-$record["term"]);
					$status = "Returned $days days late on ".date("D M j, Y",$record["date_in"]);
				}
				else $status = "Returned on time";
			}
			if ( $checkout->status($record["id"])  == "out" ) {
				$status = "Checked out";
			}
			if ( $checkout->status($record["id"])  == "late" ) {
				$days = (day_diff($record["date_out"],time())-$record["term"]);
				$status = "Overdue, $days days late";
			}
			if ( $checkout->status($record["id"])  == "way" ) {
				$days = (day_diff($record["date_out"],time())-$record["term"]);
				$status = "Way overdue, $days days late";
			}
			
			
			?>
			
			<label for="term"><b><span class="req"> </span>Status:</b>
				<input style='font-family: monospace;' readonly="readonly" id="out" name="out" type="text" class="f-name" value="<?=$status?>" /><br />
			</label>
			
			<label for="term"><b><span class="req"> </span>Checkout Date:</b>
				<input style='font-family: monospace;' readonly="readonly" id="out" name="out" type="text" class="f-name" value="<?=date("D M j, Y",$record["date_out"])?>" /><br />
			</label>
				
			<label for="term"><b><span class="req">* </span>Due Date:</b>
			<?php
			if ( $checkout->status($record["id"]) == "in" ) {
				show_dates($record["date_out"],false,$record["term"]);
			}
			else show_dates($record["date_out"],true,$record["term"]);
			?>
			</label>
			
			<label for="item"><b><span class="req">* </span>Item:</b>
			<select id="item" name="item" tabindex="3">
			<?php
			$titles = $checkout->titles->find("all");
			echo $record["title_id"];
			foreach ( $titles as $title )
			{
				if ($title["enable"] == "0" ) $disabled = " [Disabled] ";
				else $disabled = "";
				echo "<option value='".$title["id"]."'";
				if ( $record["title_id"] == $title["id"] ) echo " selected='selected'";
				echo ">".$disabled.$title["type"]." - ".$title["title"];
				echo "</option>";
			}			
			?>
			</select>
			</label>
					
			<label for="building"><b><span class="req">* </span>Building:</b>
			<select id="building" <?=$lock?> name="building" tabindex="4">
				<option <?php if ( $record["building"] == "EECS" ) echo "selected='selected'"; ?>>EECS</option>
				<option <?php if ( $record["building"] == "CSE" ) echo "selected='selected'"; ?>>CSE</option>
			</select>
			</label>
			
			<label for="note"><b>Note:</b>
				<textarea id="note" name="note" type="text" class="f-comments" rows="5" cols="20" tabindex="3"><?php echo $record["note"]; ?></textarea><br />
			</label>
			
			<div class="f-submit-wrap">
				<input type="submit" value="Update" class="f-submit" tabindex="6" /><br />
			</div>
			
			</fieldset> 
			<input type="hidden" name="agent" id="agent" value="<?php echo $record["agent"]; ?>" />
			
		</form>
		
		<?php		
	}
		
	// all actions except edit show create form as well
	if ( $action != "edit" AND $action != "remove" ) $action = "new";
	
	// show new form
	if ( $action == "new" )
	{
		?>
		
		<form action = "actions.php?destination=records.php&action=add" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>New Record</h3>
		
			<p><small>If at all possible, users should check items out themselves using the 'self 
checkout' page.<br />This minimizes typing mistakes and cases of mistaken identity. Long term and disabled item 
loans can only be created with this form.</small></p>

			<label for="user"><b><span class="req">* </span>Uniquename:</b>
				<input id="user" name="user" type="text" class="f-name" tabindex="1" /><br />
			</label>
						
			<label for="term"><b><span class="req">* </span>Due Date:</b>
			<?php
			show_dates(time(),true);
			?>
			</label>
			
			<label for="item"><b><span class="req">* </span>Item:</b>
			<select id="item" name="item" tabindex="3">
			<?php
			$titles = $checkout->titles->find("all");
			foreach ( $titles as $title )
			{
				if ($title["enable"] == "0" ) $disabled = " [Disabled] ";
				else $disabled = "";
				echo "<option value='".$title["id"]."'>".$disabled.$title["type"]." - ".$title["title"]."</option>";
			}			
			?>
			</select>
			</label>
			
			<label for="building"><b><span class="req">* </span>Building:</b>
			<select id="building" name="building" tabindex="4">
				<?php $ip = explode(".",$_SERVER['REMOTE_ADDR']); ?>
				<option <?php if ( $ip[1] == "213" ) echo " selected='selected'"; ?>>EECS</option>
				<option <?php if ( $ip[1] == "212" ) echo " selected='selected'"; ?>>CSE</option>
			</select>
			</label>
			
			<label for="note"><b>Note:</b>
				<textarea id="note" name="note" type="text" class="f-comments" rows="5" cols="20" tabindex="3"></textarea><br />
			</label>
			
			<div class="f-submit-wrap">
				<input type="submit" value="Create" class="f-submit" tabindex="6" /><br />
			</div>
	
			</fieldset> 
		</form>
		
		<?php
	}
	
	// SHOW RECORD LIST
	
	// determine view option
	if ( isset($_REQUEST["view"])) $view = $_REQUEST["view"];
	else $view = "OUT-ALL";
	
	// show view menu
	echo "<p>";
	echo "Filter records: <a href='records.php?view=OUT-ALL'>All Unreturned</a>";
	echo " | <a href='records.php?view=OUT-EECS'>EECS Unreturned</a>";
	echo " | <a href='records.php?view=OUT-CSE'>CSE Unreturned</a>";
	echo " | <a href='records.php?view=ALL-EECS'>EECS All</a>";
	echo " | <a href='records.php?view=ALL-CSE'>CSE All</a>";
	echo " | <a href='records.php?view=LONG'>Long Term Loans</a>";
	echo " | <a href='records.php?view=LATE'>Overdue</a>";
	echo " | <a href='records.php?view=TITLE'>Title Search</a>";
	echo "</p>";
	
	// show current view title	
	if ( $view == "ALL" ) echo "<h4>Showing all records</h4>";
	else if ( $view == "OUT-ALL" ) echo "<h4>Showing all unreturned records</h4>";
	else if ( $view == "OUT-EECS" ) echo "<h4>Showing all unreturned records from EECS</h4>";
	else if ( $view == "OUT-CSE" ) echo "<h4>Showing all unreturned records from CSE</h4>";
	else if ( $view == "ALL-EECS" ) echo "<h4>Showing all records from EECS</h4>";
	else if ( $view == "ALL-CSE" ) echo "<h4>Showing all records from CSE</h4>";
	else if ( $view == "LONG" ) echo "<h4>Showing all long term records</h4>";
	else if ( $view == "LATE" ) echo "<h4>Showing all overdue records</h4>";
	else if ( $view == "TITLE" ) echo "<h4>Shoing all records matching search</h4>";
	else echo "<h4>Showing unknown view</h4>";
	
	// get title id 
	if ( isset($_REQUEST["id"])) $id = $_REQUEST["id"];
	else $id = 0;
	
	// show title search form
	if ( $view == "TITLE" ) {
			?>
			<form action = "records.php?view=TITLE" method = "post" class = "f-wrap-1">
			<fieldset>
			<select id="id" name="id" tabindex="1">
			<?php
			$titles = $checkout->titles->find("all");
			foreach ( $titles as $title )
			{
				if ($title["enable"] == "0" ) $disabled = " [Disabled] ";
				else $disabled = "";
				if ( $id == $title["id"] ) $selected = "selected=\"selected\"";
				else $selected = "";
				echo "<option value='".$title["id"]."' ".$selected.">".$disabled.$title["type"]." - ".$title["title"]."</option>";
			}			
			?>
			</select>
			<input type="submit" value="Show" class="f-submit" tabindex="6" /><br />
			</fieldset>
			</form>
			<?php
	}
	
	// determine page
	if ( isset($_REQUEST["page"])) $page = $_REQUEST["page"];
	else $page = 1;
	
	// display records
	$array = $checkout->record_list($view,$page,$id);
	$records = $array["records"];
	$curr_page = $array["curr_page"];
	$last_page = $array["num_pages"];
	$here = "records.php?view=".$view."&page=$page";
	$pass = "view=".$view."&id=".$id."&page=$page";
	
	?>
	
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
				echo "<td><a href='user.php?user=".$record["user"]."'>".$record["user"]."</a></td>";
				if ( $record["term"] == 0 ) echo "<td>none</td>";
				else echo "<td>".date("n-j-y",$record["date_out"]+($record["term"]*86400))."</td>";
				echo "<td>".$record["building"]."</td>";
				echo "<td>";
				if ( $record["term"] == 0 ) echo "Long Term";
				else echo $record["term"]." Days";
				echo "</td>";
				echo "<td>";
				if ( $checkout->status($record["id"]) == "in" ) echo "<span class='success'>Returned</span>";
				if ( $checkout->status($record["id"]) == "out" ) echo "<span class='highlight'>Out</span>";
				if ( $checkout->status($record["id"]) == "late" ) echo "<span class='error'>Overdue</span>";
				if ( $checkout->status($record["id"]) == "way" ) echo "<span class='error'>Way Overdue</span>";
				echo "</td>";
				echo "<td>";
				if ( $record["status"] == "out"  ) echo "<a href='actions.php?destination=records.php&action=return&id=".$record["id"]."'>Return</a> | ";
				echo "<a href='records.php?action=edit&id=".$record["id"]."'>Edit</a> | ";
				echo "<a href='viewlog.php?term=".$record["id"]."'>Logs</a>";
				if ( $record["form_required"] ) {
					echo " | <a href='https://www.eecs.umich.edu/dco/tools/checkout/removal_form.php?id=".$record["id"]."' target='_blank'>Form</a>";
				}
				"</td>";
				echo "</tr>";
			}	
			?>
		
		</tbody>
	</table>
	<br />
	
	<?php
		
	// show pagination	
	echo "<div class='pagination'><p>";
	if ( $curr_page > 1 ) echo "<a href='".$here."&page=".($curr_page-1)."'><strong>Previous</strong></a>\n";
	else echo "<span><strong>Previous</strong></span>\n";
	
	function page_link($here,$curr,$i)
	{
		if ( $curr == $i ) echo "<span>".$i."</span>\n";
		else echo "<a href='".$here."&page=".$i."'>".$i."</a>\n";
	}
	
	if ( $last_page < 12 )
	{
		for( $i = 1; $i <= $last_page; $i++ ) page_link($here,$curr_page,$i);
	}
	else
	{
		// case 1: curr in first 6
		if ( $curr_page < 7 )
		{
			for ( $i = 1; $i < 9; $i++ ) page_link($here,$curr_page,$i);
			echo "...\n";
			for ( $i = $last_page-2; $i <= $last_page; $i++ ) page_link($here,$curr_page,$i);
		}
		// case 2: curr in last 6
		else if ( $curr_page >= ($last_page-6) )
		{
			for ( $i = 1; $i < 4; $i++ ) page_link($here,$curr_page,$i);
			echo "...\n";
			for ( $i = $last_page-7; $i <= $last_page; $i++ ) page_link($here,$curr_page,$i);
			
		}
		// case 3: curr in middle
		else
		{
			for ( $i = 1; $i < 4; $i++ ) page_link($here,$curr_page,$i);
			echo "...\n";
			for ( $i = $curr_page-2; $i <= $curr_page+2; $i++ ) page_link($here,$curr_page,$i);
			echo "...\n";
			for ( $i = $last_page-2; $i <= $last_page; $i++ ) page_link($here,$curr_page,$i);
		}
	}
		
	if ( $curr_page < $last_page ) echo "<a href='".$here."&page=".($curr_page+1)."'><strong>Next</strong></a>";
	else echo "<span><strong>Next</strong></span>";
	echo "</p>";
	echo "<h4>Page ".$curr_page." of ".$last_page."</h4>";
	echo "</div>";
		
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
		<h3>Record Management</h3>
		<p>This page allows you to fully manage checkout records.</p>
	</div>
	
	<div class="featurebox">
		<h3>New Record</h3>
		<p>To check out an item to a user (including long term loans), complete the 'New Record' form.</p>
	</div>
	
	<div class="featurebox">
		<h3>Return Item</h3>
		<p>To mark an item as returned, click the 'Return' link near it's entry in the record list. There are several view options to make finding records easier.</p>
	</div>
			
</div>
	
	
<?php require "../layout/footer.php"; ?>
