<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

require "includes/checkout.class.php"; 
require "includes/notice.class.php";
require "includes/helpers.php"; 
$checkout = new Checkout();

if ( !isset($_REQUEST["id"]) ) die("Checkout ID cannot be blank!");

$id = $_REQUEST["id"];

$notices = new Notice($checkout->record_show($id));
$content = $notices->fill_blanks("removal.template");

?>

<html>
<head>
	<title>University of Michigan Request for Removal and Use of University Property</title>
	<link rel="stylesheet" type="text/css" href="http://www.eecs.umich.edu/dco/tools/checkout/layout/css/removal.css" media="print" />
	<link rel="stylesheet" type="text/css" href="http://www.eecs.umich.edu/dco/tools/checkout/layout/css/removal.css" media="screen" />
	<script>
		function chkstate(){
			if (document.readyState=="complete"){
				window.close()
			}
			else{
				setTimeout("chkstate()",2000)
			}
		}
		function print_win(){
			window.print();
			chkstate();
		}
	</script>
</head>
<body onload="print_win()">
	<div id="print" class='prebox'>
		<p><?php echo $content; ?></p>
	</div>
</body>
</html>
