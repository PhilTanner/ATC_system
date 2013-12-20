<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$_POST['benefit'] = (isset($_POST['benefit'])&&$_POST['benefit']?1:0);
	
	if( $_POST['invoice'] )
	{
		$query = "UPDATE invoice SET amount_paid = amount_paid + ".(float)$_POST['amount']." WHERE invoice_id = ".(int)$_POST['invoice']." LIMIT 1;";
		$mysqli->query($query);
	}
	
	$query = "
INSERT INTO payments ( 
	invoice_id, 
	amount, 
	student_id, 
	reference,
	benefit_payment,
	date_received,
	period_start,
	period_end
) VALUES ( 
	".(int)$_POST['invoice'].", 
	".(float)$_POST['amount'].", 
	".(int)$_POST['student'].", 
	".(isset($_POST['reference'])?"'".$mysqli->real_escape_string($_POST['reference'])."'":"NULL").",
	".(isset($_POST['benefit'])&&$_POST['benefit']?1:0).",
	'".(isset($_POST['date_received'])&&strtotime($_POST['date_received'])?date('c',strtotime($_POST['date_received'])):date('c'))."',
	".(isset($_POST['period_start'])&&strtotime($_POST['period_start'])?"'".date('c',strtotime($_POST['period_start']))."'":"NULL").",
	".(isset($_POST['period_end'])&&strtotime($_POST['period_end'])?"'".date('c',strtotime($_POST['period_end']))."'":"NULL")."
);";
	$mysqli->query($query);
	
	if(!$_POST['benefit'])
	{
		$query = "UPDATE student SET balance = balance + ".(float)$_POST['amount']." WHERE student_id = ".(int)$_POST['student']." LIMIT 1;";
		$mysqli->query($query);
	} else {
		$query = "UPDATE student SET wins_overpayments = wins_overpayments + ".(float)$_POST['amount']." WHERE student_id = ".(int)$_POST['student']." LIMIT 1;";
		$mysqli->query($query);
	}
	
	/* close connection */
	$mysqli->close();
?>

