<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	if( file_exists('invoices/'.(int)$_GET['invoice'].'.pdf') )
		header('Location: invoices/'.(int)$_GET['invoice'].'.pdf');
	
	echo '<h1>Invoice not found!</h1>';
?>
