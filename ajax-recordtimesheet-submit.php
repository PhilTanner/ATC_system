<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	for( $i = 0; $i < count($_POST['student']); $i++ )
	{
		if( (int)$_POST['student'][$i]  && strlen( $_POST['left'][$i] ) )
		{
			$query = "
INSERT INTO attendance ( 
	`date`, 
	student_id, 
	arrived, 
	`left`,
	hourly_charge
) VALUES ( 
	'".date('Y-m-d', strtotime($_POST['datepicker'][$i]))."', 
	".(int)$_POST['student'][$i].", 
	'".$_POST['arrived'][$i]."', 
	'".$_POST['left'][$i]."',
	'".$_POST['hourly_charge'][$i]."'
);";
			// Don't save records where the student left before they arrived
			if( strtotime(date('Y-m-d', strtotime($_POST['datepicker'][$i])).' '.$_POST['arrived'][$i]) <=  strtotime(date('Y-m-d', strtotime($_POST['datepicker'][$i])).' '.$_POST['left'][$i]) )
				$mysqli->query($query);
			
			// If we have an absence - put it into the booking exceptions table to count for student numbers etc
			if( $_POST['left'][$i] == PKB_TIMEOUT_AUTH_ABS || $_POST['left'][$i] == PKB_TIMEOUT_UNAUTH_ABS )
			{
				$query = "
INSERT INTO booking_exception ( 
	student_id, 
	`date`, 
	reason 
) VALUES ( 
	".(int)$_POST['student'][$i].", 
	'".date('Y-m-d', strtotime($_POST['datepicker'][$i]))."', 
	".($_POST['left'][$i]==PKB_TIMEOUT_AUTH_ABS?"'Placement fee'":"'Session fee'")."
)";
				$mysqli->query($query);
			}
		}
	}

	/* close connection */
	$mysqli->close();
?>

