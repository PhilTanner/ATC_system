<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	if( strlen($_POST['left']) )
	{
		$query = "INSERT INTO attendance ( `date`, student_id, arrived, `left`, hourly_charge) VALUES ( '".date('Y-m-d', strtotime($_POST['datepicker']))."', ".(int)$_POST['student'].", '".$_POST['arrived']."', '".$_POST['left']."', ".$_POST['hourly_charge']." );";
		
		$mysqli->query($query);
	}
	/* close connection */
	$mysqli->close();
?>

