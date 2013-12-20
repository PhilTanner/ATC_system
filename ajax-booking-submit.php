<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	if( isset($_POST['mon']) )
	{
		$query = "
INSERT INTO recurring_booking ( 
	student_id, 
	start_date, 
	end_date, 
	mon, 
	tue, 
	wed, 
	thu, 
	fri 
) VALUES ( 
	".(int)$_POST['student'].", 
	'".date('Y-m-d', strtotime(str_replace(',','', $_POST['start_date'])))."',
	'".date('Y-m-d', strtotime(str_replace(',','', $_POST['end_date'])))."',
	".(int)$_POST['mon'].", 
	".(int)$_POST['tue'].", 
	".(int)$_POST['wed'].", 
	".(int)$_POST['thu'].", 
	".(int)$_POST['fri']." 
)";
		$mysqli->query($query);
	} elseif( isset($_POST['reason']) ) {
		$query = "
INSERT INTO booking_exception ( 
	student_id, 
	`date`, 
	reason 
) VALUES ( 
	".(int)$_POST['student'].", 
	'".date('Y-m-d', strtotime(str_replace(',','', $_POST['date'])))."',
	'".$mysqli->real_escape_string($_POST['reason'])."'
)";
		$mysqli->query($query);
	} elseif( isset($_POST['booking_del']) ) {
		$query = 'DELETE FROM recurring_booking WHERE recurring_booking_id = '.(int)$_POST['booking_del'].' LIMIT 1;';
		$mysqli->query($query);
	} elseif( isset($_POST['exception']) ) {
		$query = 'DELETE FROM booking_exception WHERE exception_id = '.(int)$_POST['exception'].' LIMIT 1;';
		$mysqli->query($query);
	}
/*	
	$query = "
INSERT INTO payments ( 
	invoice_id, 
	amount, 
	student_id, 
	reference,
	benefit_payment
) VALUES ( 
	".(int)$_POST['invoice'].", 
	".(float)$_POST['amount'].", 
	".(int)$_POST['student'].", 
	".(isset($_POST['reference'])?"'".$mysqli->real_escape_string($_POST['reference'])."'":"NULL").",
	".(isset($_POST['benefit'])&&$_POST['benefit']?1:0)."
);";
	$mysqli->query($query);
*/	
	
	/* close connection */
	$mysqli->close();
?>

