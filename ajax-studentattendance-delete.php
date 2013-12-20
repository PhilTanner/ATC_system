<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	 
	$query = "
DELETE
FROM	attendance 
WHERE 	`date` = '".$_POST['date']."'
	AND student_id = ".$_POST['student']." 
	AND arrived = '".$_POST['arrived']."'
	AND `left` = '".$_POST['left']."'
LIMIT 1;";
echo $query;
	$mysqli->query($query);
	
	/* close connection */
	$mysqli->close();
?>

