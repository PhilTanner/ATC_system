<?php
	require_once 'config.php';

	// We're going to include the invoice generate code, so that we make sure that stuff is always done the same way

	// We also want to grab the data we would be posting the to the generate invoice code	
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
	$query = "SELECT * FROM invoice WHERE invoice_id = ".(int)$_POST['invoice']." LIMIT 1;";
	if ($result = $mysqli->query($query)) 
	{
		$obj = $result->fetch_object();
	
		$_POST['reference'] = $obj->reference;
		$_POST['student'] = $obj->student_id;
		$_POST['start'] = $obj->start_date;
		$_POST['end'] = $obj->end_date;
	
		// Remove our invoice PDF file
		rename('invoices/'.(int)$_POST['invoice'].'.pdf', 'invoices/'.(int)$_POST['invoice'].'.deleted.pdf');
		
		// Reset the attendance lines so that we can re-invoice them later (and regenerate the invoice to know how much we want to credit back)
		$query = 'UPDATE `attendance` SET `invoiced_id` = NULL WHERE `invoiced_id` = '.(int)$_POST['invoice'].';';
		$mysqli->query($query);
		// Reset the payment lines so that we can re-invoice them later (and regenerate the invoice to know how much we want to credit back)
		$query = 'UPDATE `payments` SET `invoiced_id` = NULL WHERE `invoiced_id` = '.(int)$_POST['invoice'].';';
		$mysqli->query($query);
		
		// Then delete the invoice line itself
		$query = 'DELETE FROM `invoice` WHERE `invoice_id` = '.(int)$_POST['invoice'].';';
		$mysqli->query($query);
		
		/*
		// Now we've reset our database, we can pretend to regenerate the invoice - so we can work out how much we need to credit back
		// But we don't want to actually write that code to the DB, so set that value
		$recordvalues = false;
		include_once 'ajax-invoice-generate.php';
		
		// For error checking, we'll also temporarily store the invoice we've generated - it *should* match the one we were wanting to delete :D 
		$fp = fopen('invoices/tmp.pdf', 'w');
		fwrite($fp, $pdfdoc);
		fclose($fp);
		
		var_dump($invoicetotal);
		var_dump($WINSdiscounts);
		var_dump(($invoicetotal-$WINSdiscounts));
		// Now credit the debitted money back to the student
		$query = 'UPDATE `student` SET `balance` = `balance`-'.($invoicetotal).', `wins_overpayments` = `wins_overpayments`-'.$WINSdiscounts.' WHERE `student_id` = '.(int)$_POST['student'].' LIMIT 1;';
		$mysqli->query($query);
		*/
	}
	
	$mysqli->close();
	
?>

