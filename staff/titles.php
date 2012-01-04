<?php require "../layout/section_header.php"; ?>

<div id="content">
	

	<?php
	//echo "<script language=javascript>setTimeout(\"location.href='https://www.eecs.umich.edu/dco/tools/checkout/logout.php'\",120000);</script>";

	require "../includes/checkout.class.php";
	require "../includes/statistics.class.php";
	
	$checkout = new Checkout();
	
	// determine action
	$action = $_REQUEST["action"];
	if ( $action == "" ) $action = "new";
	
	// run actions
	// $type,$title,$model,$cost
	if ( $action == "add" ) {
		if ( $checkout->titles->add($_REQUEST["type"],$_REQUEST["title"],$_REQUEST["model"],$_REQUEST["cost"]) )
			echo "<p class='success'>Title created sucessfully.</p>";
		else
			echo "<p class='error'>An error occured while creating the title.</p>";
	}
	if ( $action == "remove" ) {
		if ( $_REQUEST["confirm"] )
		{
			if ( $checkout->titles->remove($_REQUEST["id"]) )
				echo "<p class='success'>Title removed sucessfully.</p>";
			else
				echo "<p class='error'>An error occured while removing the title.</p>";
		}
		else echo "<p><span class='highlight'><b>Warning:</b> This action will completely delete the title. Any records that reference it will be invalidated. Are you sure you want to continue?</span> <a href='titles.php?action=remove&id=".$_REQUEST["id"]."&confirm=true'>Yes</a> <a href='titles.php'>No</a></p>";
	}
	if ( $action == "update" ) {
		//update($id,$type,$title,$notice,$model,$cost,$form_required,$enable)
		if ( $checkout->titles->update($_REQUEST["id"],$_REQUEST["type"],$_REQUEST["title"],$_REQUEST["notice"],$_REQUEST["model"],$_REQUEST["cost"],$_REQUEST["flag"]) )
			echo "<p class='success'>Title updated secessfully.</p>";
		else
			echo "<p class='error'>An error occured while updating the title.</p>";
	}
	if ( $action == "enable" ) {
		if ( $checkout->titles->enable($_REQUEST["id"],$_REQUEST["flag"]) )
			echo "<p class='success'>Title status updated secessfully.</p>";
		else
			echo "<p class='error'>An error occured while updating the title status.</p>";
	}
	if ( $action == "form_require" ) {
		if ( $checkout->titles->update_form_required($_REQUEST["id"],$_REQUEST["flag"]) )
			echo "<p class='success'>Title form requirement updated sucesfully.</p>";
		else
			echo "<p class='error'>An error occured while updating the title form requirement.</p>";
	}
		
	// show edit form
	if ( $action == "edit" )
	{
		$id = $_REQUEST["id"];
		$title = $checkout->titles->find("id",$id);
		$title = $title[$id];

		?>
		
		
		<form action = "titles.php?action=update&id=<?php echo $title["id"]; ?>&flag=<?php echo $title["enable"]; ?>" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>Edit Title</h3>
			<p><small>This title can be <a href="titles.php?action=remove&id=<?php echo $title["id"]; ?>'>Remove</a>">removed</a>. However, it should not be done wihtout a REALLY good reason. Please disable items instead whenever possible.</small></p>
			<?php if ( $title["enable"] == "0" ) echo "<p class='error'>This title is currently disabled and will not appear on the item selection menu.</p>"; ?>	
			<label for="type"><b><span class="req">* </span>Type:</b>
			<select id="type" name="type" tabindex="1">
				<option <?php if ($title["type"] == "Accessory") echo "selected='selected'"; ?>>Accessory</option>
				<option <?php if ($title["type"] == "Computer") echo "selected='selected'"; ?>>Computer</option>
				<option <?php if ($title["type"] == "Monitor") echo "selected='selected'"; ?>>Monitor</option>
				<option <?php if ($title["type"] == "PC Parts") echo "selected='selected'"; ?>>PC Parts</option>
				<option <?php if ($title["type"] == "Software") echo "selected='selected'"; ?>>Software</option>
				<option <?php if ($title["type"] == "Tools") echo "selected='selected'"; ?>>Tools</option>
			</select>
			</label>
			
			<label for="title"><b><span class="req">* </span>Title:</b>
				<input id="title" name="title" type="text" value="<?php echo $title["title"]; ?>" class="f-name" tabindex="2" /><br />
			</label>
			
			<label for="model"><b><span class="req">* </span>Model:</b>
				<input id="model" name="model" type="text" value="<?php echo $title["model"]; ?>" class="f-name" tabindex="2" /><br />
			</label>
			
			<label for="cost"><b><span class="req">* </span>Cost:</b>
				<input id="cost" name="cost" type="text" value="<?php echo $title["cost"]; ?>" class="f-name" tabindex="2" /><br />
			</label>
			
			<label for="notice"><b>Notice:</b>
				<textarea id="notice" name="notice" type="text" class="f-comments" rows="6" cols="20" tabindex="3"><?php echo $title["notice"]; ?></textarea><br />
			</label>
			
			<div class="f-submit-wrap">
				<input type="submit" value="Update" class="f-submit" tabindex="4" /><br />
			</div>
	
			</fieldset> 
		</form>
		<?php
	}
	
	// all actions except edit show create form as well
	if ( $action != "edit" ) $action = "new";
	
	// show new form
	if ( $action == "new" )
	{
		?>
		<form action = "titles.php?action=add" method = "post" class = "f-wrap-1">
			<div class="req"><b>*</b> indicates a required field</div>
			<fieldset>
			<h3>New Title</h3>
		
			<label for="type"><b><span class="req">* </span>Type:</b>
			<select id="type" name="type" tabindex="1">
				<option>Accessory</option>
				<option>Computer</option>
				<option>Monitor</option>
				<option>PC Parts</option>
				<option>Software</option>
				<option>Tools</option>
			</select>
			</label>
			
			<label for="title"><b><span class="req">* </span>Title:</b>
				<input id="title" name="title" type="text" class="f-name" tabindex="2" /><br />
			</label>
			
			<label for="model"><b><span class="req">* </span>Model:</b>
				<input id="model" name="model" type="text" class="f-name" tabindex="3" /><br />
			</label>
			
			<label for="cost"><b><span class="req">* </span>Cost:</b>
				<input id="cost" name="cost" type="text" class="f-name" tabindex="4" /><br />
			</label>
			
			<div class="f-submit-wrap">
				<input type="submit" value="Create" class="f-submit" tabindex="5" /><br />
			</div>
	
			</fieldset> 
		</form>
		<?php
	}
	
	// list all titles
	$titles = $checkout->titles->find();
	?>
	
	<table class='table1'>
		<tbody>
			<tr>
				<th>Title</th>
				<th>Type</th>
				<th>Model</th>
				<th>Cost</th>
				<th>SPG 520.3 Status</th>
				<th>Availablity</th>
				<th>Actions</th>
			</tr>
			
			<?php
			foreach( $titles as $title )
			{
				echo "<tr>";
				echo "<th class='sub'>".$title["title"]."</th>";
				echo "<td>".$title["type"]."</td>";
				echo "<td>".$title["model"]."</td>";
				if ( $title["cost"] ) echo "<td>$".$title["cost"]."</td>";
				else echo "<td></td>";
				
				if ( $title["form_required"] == 1 ) echo "<td><b><a style='color:#c00;' href='titles.php?action=form_require&id=".$title["id"]."&flag=0'>Form Required</a></b></td>";
				else echo "<td><a style='color:#390;' href='titles.php?action=form_require&id=".$title["id"]."&flag=1'>Low Value</a></td>";
				
				if ( $title["enable"] == 1 ) echo "<td><a style='color:#390;' href='titles.php?action=enable&id=".$title["id"]."&flag=0'>Available</a></td>";
				else echo "<td><b><a style='color:#c00;' href='titles.php?action=enable&id=".$title["id"]."&flag=1'>Disabled</a></b></td>";
				
				echo "<td><a href='titles.php?action=edit&id=".$title["id"]."'>Edit</a>";
				echo "</td></tr>";
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
			<h3>Checkout Items</h3>
			<p>This page controls the items that users are able to select from the menu and checkout from DCO offices.</p>
			</div>
			
			<div class="featurebox">
			<h3>Removing Titles</h3>
			<p>Removing titles is not recommended. To prevent an item from being checked out, it should be disabled. Removing a title invalidates any old checkout records that reference it and can screw up the statistics.</p>
			</div>
			
			<div class="featurebox">
			<h3>Title Notice</h3>
			<p>Text entered in this field will be sent in an email to the user when they checkout the item. This could be used to send setup instruction, activation keys, or informtation on who to contact if they encounter a problem. If the field is blank, no notice will be sent.</p>
			</div>
			
		</div>
	
	
<?php require "../layout/footer.php"; ?>