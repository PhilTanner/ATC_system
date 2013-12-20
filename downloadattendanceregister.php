<?php
	require_once 'config.php';

	// Select the MOnday following the passed date (or today by default)
	$date = (isset($_GET['date'])&&strtotime(str_replace(',','',$_GET['date']))?strtotime(str_replace(',','',$_GET['date'])):strtotime('next Monday'));
	if( date('D', $date) != 'Mon' ) 
		$date = strtotime('next Monday', $date);

	$bookedStudents = studentBookings( date('c', $date) );
	
	require('./fpdf17/fpdf.php');
	
	class PDF extends FPDF
	{
		// Page header
		function Header()
		{
			global $date;

			$this->SetTextColor(0);
			$this->Image('logo.png',155,5,55);
			$this->SetFont('Arial','B',15);
			$this->Cell(0,8,'Weekly Attendance Register',0,1,'C');
			$this->SetFont('Arial','B',10);
			$this->Cell(0,6,'Week ..........    Term ..........    (w/c '.date('jS M Y', $date).')',0,1,'C');
			// Line break
			$this->Ln(10);
		}
	}

	// Instanciation of inherited class
	$pdf = new PDF();
	$pdf->AliasNbPages();
	$pdf->SetLeftMargin(20);
	$pdf->AddPage();
	$pdf->SetFont('Arial','',9);
	
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);

	/* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	$query = "
SELECT	firstname, 
	lastname,
	(
		SELECT	COUNT(recurring_booking_id)
		FROM	recurring_booking
		WHERE	recurring_booking.student_id = student.student_id
			AND start_date <= '".date('Y-m-d', ($date+(21*24*60*60)))."'
			AND end_date >= '".date('Y-m-d', ($date-(7*24*60*60)))."'
	) AS bookings,
	(
		SELECT	COUNT(exception_id)
		FROM	booking_exception
		WHERE	booking_exception.student_id = student.student_id
			AND `date` <= '".date('Y-m-d', ($date+(21*24*60*60)))."'
			AND `date` >= '".date('Y-m-d', ($date-(7*24*60*60)))."'
	) AS exceptions
FROM 	student 
ORDER BY lastname, firstname;";

	if ($students = $mysqli->query($query))
	{
		$totals = array( 0, 0, 0, 0, 0 );
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(82,7,'',1);
		$pdf->Cell(20,7,'Mon',1,0,'C');
		$pdf->Cell(20,7,'Tue',1,0,'C');
		$pdf->Cell(20,7,'Wed',1,0,'C');
		$pdf->Cell(20,7,'Thu',1,0,'C');
		$pdf->Cell(20,7,'Fri',1,0,'C');
		$pdf->Cell(1,7,'',0,1);
		
		$pdf->SetFont('Arial','',10);
		
		$outputrows = 0;

		while ($obj = $students->fetch_object())
		{
			if( !(int)$obj->bookings && !(int)$obj->exceptions) continue;
			$outputrows++;
			$pdf->SetFont('Arial','B',11.5);
			$pdf->SetTextColor(0);
			$pdf->Cell(47,5,$obj->lastname,1);
			$pdf->Cell(35,5,$obj->firstname,1);
			for( $i=0; $i<=4; $i++ )
			{
				$pdf->SetFillColor(255);
				$letter = '';
				$pdf->SetFont('Arial','',10);
				for( $n=0; $n<count($bookedStudents[$i]); $n++ )
				{
					if( $bookedStudents[$i][$n][0] == BOOKED && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname )
					{
						//$pdf->SetFillColor(153,153,255);
						//$pdf->SetTextColor(200,200,255);
						$pdf->SetTextColor(153,153,255);
						$letter = 'Booked';
						$totals[$i]++;
						//break;
					} elseif( $bookedStudents[$i][$n][0] == ABSENT && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname ) {
						$pdf->SetFillColor(255,255,153);
						$letter = $bookedStudents[$i][$n][2];
						$pdf->SetFont('Arial','',4);
						$pdf->SetTextColor(0);
						if( !$letter )
							$letter = 'A'; 
					} elseif( $bookedStudents[$i][$n][0] == REQUEST && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname ) {
						$pdf->SetFillColor(255,153,153);
						$letter = $bookedStudents[$i][$n][2];
						$pdf->SetFont('Arial','',4);
						$pdf->SetTextColor(0);
						if( !$letter )
							$letter = 'R'; 
						$totals[$i]++;
					} 
				}
				
				$pdf->Cell(15,5,$letter,1,0,'C',true);
				$pdf->SetFillColor(183);
				$pdf->Cell(5,5,' ',1,0,'L',true);
			}
			$pdf->Cell(1,5,'',0,1);
		}
	}
	
	// Spare children lines
	$a = $outputrows;
	while( $a % 45 )
	{
		$a++;
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(47,5,'',1);
		$pdf->Cell(35,5,'',1);
		for( $i=0; $i<=4; $i++ )
		{
			$pdf->SetFillColor(255);
			$letter = '';
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(15,5,$letter,1,0,'C',true);
			$pdf->SetFillColor(183);
			$pdf->Cell(5,5,' ',1,0,'L',true);
		}
		$pdf->Cell(1,5,'',0,1);
	}

	// Totals
	$pdf->SetFont('Arial','B',9);
	$pdf->SetFillColor(255);
	$pdf->SetTextColor(200);
	$pdf->Cell(82,5,'Totals',1);
	for( $i=0; $i<=4; $i++ )
		$pdf->Cell(20,5,$totals[$i],1,0,'C',true);
	$pdf->Cell(1,5,'',0,1);

	// Output children with no bookings
	if ($students = $mysqli->query($query))
	{
		$pdf->AddPage(); // Pagebreak
		$totals = array( 0, 0, 0, 0, 0 );
		
		$pdf->SetTextColor(180);
		$pdf->SetDrawColor(180);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(82,7,'',1);
		$pdf->Cell(20,7,'Mon',1,0,'C');
		$pdf->Cell(20,7,'Tue',1,0,'C');
		$pdf->Cell(20,7,'Wed',1,0,'C');
		$pdf->Cell(20,7,'Thu',1,0,'C');
		$pdf->Cell(20,7,'Fri',1,0,'C');
		$pdf->Cell(1,7,'',0,1);
		
		$pdf->SetFont('Arial','',9);

		while ($obj = $students->fetch_object())
		{
			if( (int)$obj->bookings || (int)$obj->exceptions) continue;
			$pdf->SetFont('Arial','B',11.5);
			$pdf->Cell(47,5,$obj->lastname,1);
			$pdf->Cell(35,5,$obj->firstname,1);
			for( $i=0; $i<=4; $i++ )
			{
				$pdf->SetFillColor(255);
				$letter = '';
				$pdf->SetFont('Arial','',9);
				for( $n=0; $n<count($bookedStudents[$i]); $n++ )
				{
					if( $bookedStudents[$i][$n][0] == BOOKED && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname )
					{
						//$pdf->SetFillColor(153,153,255);
						//$pdf->SetTextColor(200,200,255);
						$pdf->SetTextColor(153,153,255);
						$letter = 'B';
						$totals[$i]++;
						break;
					} elseif( $bookedStudents[$i][$n][0] == ABSENT && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname ) {
						$pdf->SetFillColor(255,255,153);
						$letter = $bookedStudents[$i][$n][2];
						$pdf->SetFont('Arial','',4);
						if( !$letter )
						{ 
							$letter = 'A'; 
							$pdf->SetFont('Arial','',9);
							$pdf->SetTextColor(225,225,53);
						}
					} elseif( $bookedStudents[$i][$n][0] == REQUEST && $bookedStudents[$i][$n][1] == $obj->lastname.', '.$obj->firstname ) {
						$pdf->SetFillColor(255,153,153);
						$letter = $bookedStudents[$i][$n][2];
						$pdf->SetFont('Arial','',4);
						if( !$letter )
						{ 
							$letter = 'R'; 
							$pdf->SetFont('Arial','',9); 
							$pdf->SetTextColor(255,200,200);
						}
						$totals[$i]++;
					} 
				}
				
				$pdf->Cell(15,5,$letter,1,0,'C',true);
				$pdf->SetFillColor(200);
				$pdf->Cell(5,5,' ',1,0,'L',true);
			}
			$pdf->Cell(1,5,'',0,1);
		}
	}
			
	$pdf = $pdf->Output('Attendance Register '.date('Y-m-d', $date).'.pdf','D');
?>
