<?php require "layout/section_header.php"; ?>

<?php 

require "includes/checkout.class.php"; 
require "includes/notice.class.php";
require "includes/helpers.php"; 
$checkout = new Checkout();

?>

<div id="content">
		
	<?php
	require "includes/file.class.php";
	$files = new File();

	if ( !isset($_REQUEST["action"]) ) $_REQUEST["action"] = "";
	
	if ($files->search("includes/checkout.access",$_SERVER['REMOTE_ADDR']." ") OR $_SERVER['REMOTE_USER'] == "mcolf")
	{
		if ( $_REQUEST["action"] == "create" )
		{
			// check for valid uniquename
			$user = $_REQUEST["user"];
			valid_uniquename($user);
			
			if ( $user != "" AND valid_uniquename($user) )
			{
				// check for account holds
				if ( !$checkout->holds($user) )
				{
					$ip = $_SERVER["REMOTE_ADDR"];
					$ip = explode(".",$ip);
					// check building
					$building = "";
					if ( $ip[1] == "213" ) $building = "EECS";
					else $building = "CSE";
					
					//echo "User: ".$user." ";
					//echo "Item: ".$_REQUEST["item"]." ";
					//echo "Note: ".$_REQUEST["note"]." ";
					//echo "Building: ".$building." ";
					//echo "Remote: ".$_SERVER['REMOTE_USER']." ";
					//echo "Term: ".$_REQUEST["term"]." ";
					
					// check out item
					if ( $checkout->add($user,$_REQUEST["item"],$_REQUEST["note"],$building,$_SERVER['REMOTE_USER'],$_REQUEST["term"]) )
					{
						// is a property removal form required?
						if ( $checkout->titles->form_required($_REQUEST["item"]) == 1 ) 
						{	
							?>
							
							<div class='successbox'>
							<h3>Success</h3>
							<p>Your loan request has been approved. Please return this item by <?php echo date("l F, j Y",time()+($_REQUEST["term"]*86400)); ?></p>
							<p><b>Important!</b> University of Michigan property control guidlines (<a href="http://spg.umich.edu/section/520/">SPG 520.2</a>) require that certain paperwork be completed for loans of this nature.
							Please note that we are required to inform the Department of Public Safety and Risk Management if this equipment is not returned in a timely manner.</p>
							<p>Once you have followed the directions below to complete the required paperwork, please choose one of the options below.</p>
							<a href='checkout.php' class='greenbutton'>Checkout Another Item</a><a href='logout.php' class='button'>Logout</a>
							</div>

							<?php
							// prepare form for user to print
							$notices = new Notice($checkout->record_show($checkout->last_id));
							$content = $notices->fill_blanks("removal.template");
							?>
							
							<script type="text/javascript">
							<!--
							function printContent(id){
								str=document.getElementById(id).innerHTML
								newwin=window.open('','printwin','left=100,top=100,width=400,height=400')
								newwin.document.write('<HTML>\n<HEAD>\n')
								newwin.document.write('<TITLE>University of Michigan Request for Removal and Use of University Property</TITLE>\n')
								newwin.document.write('<script>\n')
								newwin.document.write('function chkstate(){\n')
								newwin.document.write('if(document.readyState=="complete"){\n')
								newwin.document.write('window.close()\n')
								newwin.document.write('}\n')
								newwin.document.write('else{\n')
								newwin.document.write('setTimeout("chkstate()",2000)\n')
								newwin.document.write('}\n')
								newwin.document.write('}\n')
								newwin.document.write('function print_win(){\n')
								newwin.document.write('window.print();\n')
								newwin.document.write('chkstate();\n')
								newwin.document.write('}\n')
								newwin.document.write('<\/script>\n')
								newwin.document.write('<link rel=\'stylesheet\' type=\'text/css\' href=\'http://www.eecs.umich.edu/dco/tools/checkout/layout/css/removal.css\' media=\'print\' />\n');
								newwin.document.write('<link rel=\'stylesheet\' type=\'text/css\' href=\'http://www.eecs.umich.edu/dco/tools/checkout/layout/css/removal.css\' media=\'screen\' />\n');
								newwin.document.write('</HEAD>\n')
								newwin.document.write('<BODY onload="print_win()">\n')
								newwin.document.write(str)
								newwin.document.write('</BODY>\n')
								newwin.document.write('</HTML>\n')
								newwin.document.close()
							}
							//-->
							</script>
							
							<div class='successbox'>
							<h3>Additional Paperwork Required</h3>
								<p>Please complete the directions below.</p>
								<ol>
									<li>Review the requester agreement below.
									<li>Click <a href="#null" onclick="printContent('print')">here</a> to print the document.
									<li>Sign and date in the "Agreement by Requester" section.
									<li>Hand the completed document to a DCO staff member for approval.
								</ol>
								<div id="print" class='prebox'>
									<p><?php echo $content; ?></p>
								</div>
								<br />
							</div>
							
							<?php
							
							
						}
						else 
						{
							echo "<div class='successbox'>";
							echo "<h3>Success</h3>";
							echo "<p>Your loan request has been approved. Please return this item by ".date("l F, j Y",time()+($_REQUEST["term"]*86400)).".</p>";
							echo "<p>Would you like to check out another item? If not, please logout now.</p>";
							echo "<a href='checkout.php' class='greenbutton'>Checkout Another Item</a><a href='logout.php' class='button'>Logout</a>";
							echo "</div>";
						}
					}
					else
						echo "<p class='error'>An error occured while creating the record. Please <a href='checkout.php'>try again</a>.</p>";
					
		
					// send email to user
					$notices = new Notice($checkout->record_show($checkout->last_id));
					if ( !$notices->send("start.template") ) echo "<p class='error'>An error occured when sending the message.</p>";
					echo "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
				}
				else echo "<p class='error'>A hold on your account is preventing you from checking out any additional items. Please review <a href='account.php?user=".$user."'>your account</a> and return any overdue items.</p><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
			} else echo "<p class='error'>A valid UMICH or EECS account is required. Please <a href='checkout.php'>try again</a>.</p><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
			// refresh page
			//echo "<script language=javascript>setTimeout(\"location.href='http://www.eecs.umich.edu/dco/tools/checkout/index.php'\",60000);</script>";
		}
		else
		{
			?>
			
			<p>Choose an item from the drop-down menu below and then submit to request a loan.</p>
			<form action = "checkout.php?action=create" method = "post" class = "f-wrap-1">
				<div class="req"><b>*</b> indicates a required field</div>
				<fieldset>
				<h3>Checkout</h3>

				<input type="hidden" id="user" name="user" value="<?php echo $_SERVER['REMOTE_USER']; ?>" />
				
				<label for="item"><b><span class="req">* </span>Item:</b>
					<select id="item" name="item" tabindex="2">
					<?php
					$titles = $checkout->titles->find("enabled");
					foreach ( $titles as $title )
					{
						echo "<option value='".$title["id"]."'>".$title["type"]." - ".$title["title"]."</option>";
					}			
					?>
					</select>
					<br />
				</label>
				
				<label for="term"><b><span class="req"> </span>Due Date:</b>
				<?php
				show_dates(time(),false);
				?>
				</label>
				
				<!--<label for="note"><b> Note:</b>
					<textarea id="note" name="note" type="text" class="f-comments" rows="6" cols="20" tabindex="3"></textarea><br />
				</label>-->
				
				<input type="hidden" id="note" name="note" value="" />
				<br />
				
			
				<div class="f-submit-wrap">
					<input type="submit" value="Request Loan" class="f-submit" tabindex="4" /><br />
				</div>
			
				</fieldset> 
			</form>
			
		<?php
		}
	} 
	else echo "<p class='error'>Your machine is not allowed to access this page.</a><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
	?>
	
	<br />
	<br />
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
			<h3>Loan Length</h3>
			<p>All loaned items must be returned to this office by the selected due date. Longer loans may be arranged by speaking with a staff member.</p>
			</div>
			
			<div class="featurebox">
			<h3>Microsoft Software</h3>
			<p>These titles may <strong>only</strong> be installed on machines owned by, and purchased with funds from, the University of Michigan.</p>
			</div>
			
		</div>
	
	
<?php require "layout/footer.php"; ?>