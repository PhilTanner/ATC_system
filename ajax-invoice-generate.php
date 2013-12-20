<?php

	$invoiceVersion = 3.06;
	
	/*
		Version history:
		3.06 - 2013-08-06 - Added PKB_TIMEOUT_AUTH_ABS to allow differentiation between auth'd 
		                    and unauth'd absences for charging as per new T&C
	*/
	
	require_once './config.php';
	require_once './pkbasc.class.php';
	require_once './fpdf17/fpdf.php';

	$PKBASC = new PKBASC();

	$student = $PKBASC->getStudentDetails($_POST['student']);
	$invoiceID = $PKBASC->createInvoice();
	
	if( !isset($_POST['reference']) || !strlen(trim($_POST['reference'])) )
		$_POST['reference'] = '#'.$invoiceID;

	if( !isset( $recordvalues ) ) 
		$recordvalues = true;
	
	class PDF extends FPDF
	{
		// Page header
		function Header()
		{
			global $student, $invoiceVersion, $invoiceID;

			$this->SetFont('Arial','',5);
			$this->SetTextColor(164);
			$this->Cell(0,0,$invoiceVersion.'-'.$invoiceID.'-'.date('Ymd'),0,1);
			$this->SetTextColor(92);
			$this->Image('logo.png',155,5,55);
			$this->SetFont('Arial','B',15);
			$this->Cell(0,10,'Invoice for '.$student->firstname.' '.$student->lastname,0,1,'C');
			$this->Cell(0,3,'Reference: '.trim($_POST['reference']),0,1,'C');
			$this->SetFont('Arial','B',10);
			$this->Cell(0,10,'Payment due within 7 days from receipt of invoice',0,1,'C');
			// Line break
			$this->Ln(20);
		}
		
		// Page footer
		function Footer()
		{
			$this->SetTextColor(92);
			// Position at 3.5 cm from bottom
			$this->SetY(-35);
			$this->SetFont('Arial','I',8);
			// Page number
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,1,'C');
			
			$this->SetFont('Arial','B',8);
			$this->Cell(0,3,'Payments can be made directly to Pukerua Bay After School Care Group (Westpac Porirua 031533-0018611-00)',0,1);
			$this->Cell(0,4,'Reference: child\'s name & confirm payment with email to pkbafterschoolcare@gmail.com or cash or cheque directly to Sue.',0,1);
		}
	}
	
	// Instanciation of inherited class
	$pdf = new PDF();
	$pdf->SetAutoPageBreak(true, 50);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',9);
	$pdf->SetTextColor(92);
	
	/*
	// Opening balances are shown as of the last invoiced date - or never (i.e. we've not invoiced them, they don't know they owe anything)
	$lastinvoice = $PKBASC->getLastInvoiceForStudent( $student->student_id );
	if( $lastinvoice && isset($lastinvoice->end_date) ) 
		$lastinvoiceddate = strtotime(str_replace(',','',$_POST['start'])); //strtotime( $lastinvoice->end_date );
	else 	$lastinvoiceddate = 0;
	*/
	$lastinvoiceddate = strtotime(str_replace(',','',$_POST['start'])); //strtotime( $lastinvoice->end_date );

	$startbalance = $PKBASC->getBalanceForStudent( 
		$student->student_id, 
		date('c', strtotime('1970-01-01')), 
		date('c', ($lastinvoiceddate-(1))), 
		PKB_INVOICE_INCLUDE // We only want to include charges that have been invoiced
	);
	$endbalance   = $PKBASC->getBalanceForStudent( 
		$student->student_id, 
		date('c', strtotime(str_replace(',','',$_POST['start']))), 
		date('c', strtotime(str_replace(',','',$_POST['end']))), 
		PKB_INVOICE_EXCLUDE // We only want to include charges that have not yet been invoiced
	);
	//$allbalance   = $PKBASC->getBalanceForStudent( $student->student_id, date('c', 0), date('c', strtotime(str_replace(',','',$_POST['end']))), PKB_INVOICE_BOTH );
	
	$pdf->SetTextColor(0);
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(130,5,($startbalance['FinalBalance']<0?'Outstanding':'Opening').' balance'.(strtotime($startbalance['CorrectAsOf'])?' (as of '.date('jS M Y', strtotime($startbalance['CorrectAsOf'])).')':''),'TB',0);
	$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, $startbalance['FinalBalance']),'TB',1,'R');
	$pdf->Cell(130,10,'',0,1);
	$pdf->SetTextColor(92);
	
	if( count($endbalance['Charges']) > 1 )
	{
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(0,5,'New charges from '.date('jS M Y',  strtotime(str_replace(',','',$_POST['start']))).' to '.date('jS M Y', strtotime(str_replace(',','',$_POST['end']))).'',0,1);
		$pdf->SetFont('Arial','I',7);
		$pdf->Cell(0,3,'* denotes minimum charge',0,1);

		$pdf->SetFont('Arial','',9);
		foreach( $endbalance['Charges'] as $obj )
		{
			if( !isset($obj->date) ) continue;
			
			if( substr($obj->left,0,5) == PKB_TIMEOUT_AUTH_ABS )
			{
				$pdf->SetFont('Arial','I',9);
				$pdf->Cell(10,5,'',0,0);
				$pdf->Cell(120,5,date('jS M Y', strtotime($obj->date)).' Placement fee',0,0);
				$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, (0-$obj->charge)),0,1,'R');
			} elseif( substr($obj->left,0,5) == PKB_TIMEOUT_UNAUTH_ABS ){
				$pdf->SetFont('Arial','I',9);
				$pdf->Cell(10,5,'',0,0);
				$pdf->Cell(120,5,date('jS M Y', strtotime($obj->date)).' Non-attended booked session fee',0,0);
				$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, (0-$obj->charge)),0,1,'R');
			} else {
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(10,5,'',0,0);
				$pdf->Cell(120,5,date('jS M Y', strtotime($obj->date)).' '.substr($obj->arrived,0,5).'-'.substr($obj->left,0,5).', '.$obj->duration.' hours @ '.currency_format(MONEYFORMAT, $obj->hourly_charge).' p/h',0,0); // Money format, don't say "Credit"
				$pdf->Cell(0,5,($obj->duration < 1?'*':'').currency_format(MONEYFORMAT_TEXTUAL, (0-$obj->charge)),0,1,'R');
			}
		}
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(130,5,'',0,0);
		$pdf->Cell(0,5,currency_format(MONEYFORMAT, $endbalance['ChargeTotal']),'T',1,'R');

		$pdf->Cell(130,5,'',0,1);
	}
	
	if( $endbalance['WINSDiscount'] )
	{
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(130,5,'WINS Contributions',0,0);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, $endbalance['WINSDiscount']),0,1,'R');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(130,5,'Charges not covered by WINS Contributions',0,0);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,currency_format(MONEYFORMAT, ($endbalance['ChargeTotal']-$endbalance['WINSDiscount'])),'T',1,'R');

		$pdf->Cell(130,5,'',0,1);
	}
	
	// If they're in credit, there's no owing.
	$amountOwed = ($startbalance['FinalBalance']<0?$startbalance['FinalBalance']:0);
	$amountOwed += 0-($endbalance['ChargeTotal']-$endbalance['WINSDiscount']);
	$pdf->SetTextColor(0);
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(130,5,'Total charges for this invoice','TB',0);
	$pdf->Cell(0,5,currency_format(MONEYFORMAT, 0-$amountOwed),'TB',1,'R');
	$pdf->Cell(130,10,'',0,1);
	$pdf->SetTextColor(92);
	
	if( count($endbalance['Payments']) > 1 )
	{
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(0,5,'Payments received',0,1);
		$pdf->SetFont('Arial','',9);
		foreach( $endbalance['Payments'] as $obj )
		{
			if( !isset($obj->date_received) ) continue;
			$pdf->Cell(10,5,'',0,0);
			$pdf->Cell(120,5,date('jS M Y', strtotime($obj->date_received)).' '.$obj->reference,0,0);
			$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, $obj->amount),0,1,'R');
		}
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(130,5,'',0,0);
		$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, $endbalance['AmountPaid']),'T',1,'R');
		$pdf->Cell(130,5,'',0,1);
	}

	
	// Now we need to look for out of sequence stuff, payments received before the invoice date but entered into the system after it
	$outoforderbalance = $PKBASC->getBalanceForStudent( 
		$student->student_id, 
		date('c', strtotime('1970-01-01')), 
		date('c', strtotime($startbalance['CorrectAsOf'])), 
		PKB_INVOICE_BOTH 
	);
	if( $outoforderbalance['FinalBalance'] != $startbalance['FinalBalance'] )
	{
		//var_dump($outoforderbalance);
		//var_dump($startbalance);
		//exit;
		
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(130,5,'Adjustments against previous invoiced figures','TB',0);
		$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, ($outoforderbalance['FinalBalance']-$startbalance['FinalBalance'])),'TB',1,'R');
		$pdf->Cell(130,10,'',0,1);
		$pdf->SetTextColor(92);
		
		// Adjust the balance for the final calculation is correct
		$startbalance['FinalBalance'] = $outoforderbalance['FinalBalance'];
		
		// Now append any lines we didn't know about onto this invoice so we don't continually re-charge for them
		foreach( $outoforderbalance['Charges'] as $charge )
			if( isset( $charge->charge ) && array_search( $charge, $startbalance['Charges']) === false )
				$endbalance['Charges'][] = $charge;
		foreach( $outoforderbalance['Payments'] as $payment )
			if( isset( $payment->amount ) && array_search( $payment, $startbalance['Payments']) === false )
				$endbalance['Payments'][] = $payment;
	}
	
	
	// Positive amount is credit. Negative is owed - balance, not outstanding
	$pdf->SetTextColor(0);
	$pdf->SetFont('Arial','B',11);
	if( ($startbalance['FinalBalance']+$endbalance['FinalBalance']) < 0 )
		$pdf->Cell(130,5,'Monies owed','TB',0);
	else
		$pdf->Cell(130,5,'Your new balance','TB',0);
	$pdf->Cell(0,5,currency_format(MONEYFORMAT_TEXTUAL, ($startbalance['FinalBalance']+$endbalance['FinalBalance'])),'TB',1,'R');
	
	//var_dump($startbalance);
	//var_dump($endbalance);

	
	$pdfdoc = $pdf->Output('','S');
	
	$PKBASC->saveInvoiceDetails(
		$invoiceID,
		$student->student_id, 
		$_POST['reference'], 
		date('c', strtotime(str_replace(',','',$_POST['start']))), 
		date('c', strtotime(str_replace(',','',$_POST['end']))), 
		array($startbalance,$endbalance), 
		$pdfdoc 
	);
	
	/*
	echo '<pre>';
	var_dump($startbalance);
	var_dump($endbalance);
	var_dump($balanceToday);
	echo '</pre>';
	*/


	header('Content-type: application/pdf');
	echo $pdfdoc;
	
	exit();
?>

