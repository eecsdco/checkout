<?php 
	function prefix($page = "") {
		$URI = explode("/",$_SERVER["REQUEST_URI"]); 
		$parts = explode("?",$URI[4]);
		$curr = $parts[0];
		
		if ( $page == "" ) {
			$location = "";
			if ( $curr == "index.php" OR $curr == "checkout.php" OR $curr == "account.php" OR $curr == ""  OR $curr == "samples.php" OR $curr == "stats.php") echo "";
			else echo "../";
		}
		else {
			if ( $page == $curr OR ($page == "index.php" AND $curr == "") ) echo "class = 'active'";
			else echo "";
		}		
	}
	
	function url($file = "") {
		$URI = explode("/",$_SERVER["REQUEST_URI"]);
		echo $URI[3];
		echo "www.eecs.umich.edu".$URI[1]."/".$URI[2]."/".$URI[3]."/".$file;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--
Copyright: Daemon Pty Limited 2006, http://www.daemon.com.au
Community: Mollio http://www.mollio.org $
License: Released Under the "Common Public License 1.0", 
http://www.opensource.org/licenses/cpl.php
License: Released Under the "Creative Commons License", 
http://creativecommons.org/licenses/by/2.5/
License: Released Under the "GNU Creative Commons License", 
http://creativecommons.org/licenses/GPL/2.0/
-->
<head>

<!-- DCO Web Stats -->
<?php @include_once( "/w/web/dco/staff/stats/stats_include.php" ); ?>

<!-- Google Analytics 7 -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ?
"https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost +
"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DCO Checkout System</title>
<link rel="stylesheet" type="text/css" href="<?php prefix(); ?>layout/css/main.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php prefix(); ?>layout/css/print.css" media="print" />
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" href="<?php prefix(); ?>layout/css/ie6_or_less.css" />
<![endif]-->
<script type="text/javascript" src="<?php prefix(); ?>layout/js/common.js"></script>
</head>
<body id="type-d">
<div id="wrap">

<div id="header">
	<div id="site-name">DCO Checkout System</div>
	<?php if ( $_SERVER['REMOTE_USER'] ) { ?>
	<div id="userbar">
		<a class="button" href="https://www.eecs.umich.edu/dco/tools/checkout/logout.php">Logout</a> Logged in as <strong><?php echo $_SERVER['REMOTE_USER']; ?></strong>.
	</div>
	<?php } ?>
	<ul id="nav">
		<li <?php prefix("index.php"); ?>><a href="http://www.eecs.umich.edu/dco/tools/checkout/index.php">Home</a></li>
		<li <?php prefix("stats.php"); ?>><a href="http://www.eecs.umich.edu/dco/tools/checkout/stats.php">Statistics</a></li>
		<li <?php prefix("checkout.php"); ?>><a href="http://www.eecs.umich.edu/dco/tools/checkout/checkout.php">Self Checkout</a></li>
		<li <?php prefix("account.php"); ?>><a href="https://www.eecs.umich.edu/dco/tools/checkout/account.php">My Account</a></li>
		<li <?php prefix("staff"); ?>><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/records.php">DCO Staff</a>
			<ul>
			<li class="first"><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/records.php">Record Management</a></li>
			<li><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/user.php">User Search</a></li>
			<li><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/baddies.php">Top Users</a></li>
			<li><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/titles.php">Manage Titles</a></li>
			<li><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/notices.php">Manage Notices</a></li>
			<li><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/viewlog.php">Log File Search</a></li>
			<li class="last"><a href="https://www.eecs.umich.edu/dco/tools/checkout/staff/access.php">Manage Access Lists</a></li>
			</ul>
		</li>
	</ul>
</div>

<noscript>
<div class="noticebox"><p><b>WARNING</b> Javascript must be enabled for the checkout system to work properly. Some functionality has been disabled.</p></div>
</noscript>
	
<div id="content-wrap">
