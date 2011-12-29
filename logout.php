<?php
    $central = "https://weblogin.umich.edu/cosign-bin/logout?http://www.eecs.umich.edu/dco/tools/checkout/";

	echo "LOGOUT<br />";
	
    session_unset();
	session_destroy();
	setcookie( $_SERVER[ 'COSIGN_SERVICE' ], "null", time()-1, '/', "", 1 );
	setcookie( "cosign-www.eecs", "null", time()-3600 );
	setcookie( "cosign-www.eecs", "null", time()-3600, "/", "" );
		
    /* make any local additions here (e.g. expiring local sessions, etc.),
       but it's important that there be no output on this page. */

    header( "Location: $central" );
    exit;
?>