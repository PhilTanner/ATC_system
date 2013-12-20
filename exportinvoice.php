<?php
	require_once 'config.php';
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	 
	$query = "
SELECT	PDF, invoice_id 
FROM 	invoice 
WHERE 	PDF IS NOT NULL;";
				
	if ($result = $mysqli->query($query)) 
	{
		/* fetch object array */
		while ($obj = $result->fetch_object()) 
		{
			$fp = fopen('invoices/'.$obj->invoice_id.'.pdf', 'w');
			fwrite($fp, $obj->PDF);
			fclose($fp);

			//echo ;
			
		}
	}
?>
