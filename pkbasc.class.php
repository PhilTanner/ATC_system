<?php
	require_once 'config.php';
	
	class PKBASCException extends Exception {
		/**
		 * Pretty prints the exception for the user to see
		 */
		public function toString() {
			$t = $this->getTrace();
			if (empty($t)) {
				$class = "Unknown";
			}
			else {
				$first = reset($t);
				$class = $first["class"];
			}
			return sprintf("EXCEPTION:PKBASC:%s", $class, htmlentities($this->getMessage()));
		}
	}
	class PKBASCExceptionBadData extends PKBASCException {}
	class PKBASCExceptionDBConn extends PKBASCException {}
	
	define( 'PKB_INVOICE_INCLUDE', 0 );
	define( 'PKB_INVOICE_EXCLUDE', 1 );
	define( 'PKB_INVOICE_BOTH',    2 );
	
	define( 'PKB_TIMEOUT_AUTH_ABS',  '15:10' );
	define( 'PKB_TIMEOUT_UNAUTH_ABS','15:00' );

	class PKBASC
	{
		protected static $mysqli;
		public static $dbUpToDate = false;
		
		public function __construct()
		{
			recordTimestamp('PKBASC starting');
			self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
			recordTimestamp('mysqli initiated');
			/* check connection */
			if (mysqli_connect_errno())
			    throw new PKBASCExceptionDBConn(mysqli_connect_error());

			// Find out if we're using the latest version....		    
		    	$dbtime = -1;
			if ($result = self::$mysqli->query("SELECT MAX(`date`) AS a FROM `version` LIMIT 1;"))
			{
				$row = $result->fetch_object();
				$dbtime = strtotime($row->a);
			}
			
			if ($handle = opendir('./backups/')) 
			{
				$files = array();
				while (false !== ($entry = readdir($handle))) 
					if( substr($entry,0,6) == "pkbasc" ) 
						$files[] = $entry;
				closedir($handle);
		
				arsort($files);
				foreach($files as $entry)
				{
					$backuptime = substr($entry, 7, 10);
					break;
				}
			}
			
			if( $dbtime ==  $backuptime ) 
				self::$dbUpToDate = true; 
		}
		
		public function backup( $automatic = true )
		{
			$time = time();

			if($automatic)	exec( CMD_BACKUP_AUTOMATED . $time . '.sql' );
			else {
				self::$mysqli->query('UPDATE `version` SET `date` = \''.date('c',$time).'\';');
				exec( CMD_BACKUP . $time . '.sql' );
			}
		}
		
		/*
		public function __destruct()
		{
			//self::$mysqli->close();
		}
		*/
		function currency_format( $format, $amount )
		{
			$str = '';
			switch($format)
			{
				case MONEYFORMAT_PARENTHESIS:
					if( (float)$amount < 0 )
						$str .= '(';
					$str .= '$ ';
	
					if( (float)$amount < 0 )
						$str .= number_format( (0-(float)$amount), 2, '.', ',' );
					else
						$str .= number_format( (float)$amount, 2, '.', ',' );
					if( (float)$amount < 0 )
						$str .= ')';
					break;
				case MONEYFORMAT_TEXTUAL:
					$str .= '$ ';
					
					if( (float)$amount == 0 )
						$str .= '0.00';
					else if( (float)$amount < 0 )
						$str .= number_format( (0-(float)$amount), 2, '.', ',' );
					else
						$str .= number_format( (float)$amount, 2, '.', ',' ).' cr';
					break;
				case MONEYFORMAT:
				default:
					$str .= '$ ';
					$str .= number_format( (float)$amount, 2, '.', ',' );
					break;
			}
			return $str;
		}
		
		// How much does the student owe for being at ASC between these two dates
		public function getAttendanceChargesForStudent( $student_id, $startDate, $endDate, $includeInvoicedAttendances=PKB_INVOICE_BOTH )
		{
			$sql = 'SELECT DISTINCT * FROM `attendance` WHERE `date` BETWEEN \''.date('c', strtotime($startDate)).'\' AND \''.date('c', strtotime($endDate)).'\' AND `student_id` = '.(int)$student_id;
			
			switch( $includeInvoicedAttendances )
			{
				case PKB_INVOICE_INCLUDE:
					$sql .= ' AND invoiced_id IS NOT NULL ';
					break;
				case PKB_INVOICE_EXCLUDE:
					$sql .= ' AND invoiced_id IS NULL ';
					break;
				default:
					break;
			} 
			$sql .= ' ORDER BY `date` ASC;';
			$charges = array($sql);
			if( $result = self::$mysqli->query($sql) )
			{
				while ($obj = $result->fetch_object())
				{
					$original_obj = clone($obj);
					
					$timeArrived  = strtotime( $obj->date.' '.$obj->arrived );
					// Time before 1800 is charged at normal rates.  Time after this point is charged differently
					if( strtotime( $obj->date.' '.$obj->left ) > strtotime( $obj->date.' 18:00:00' ) )
						$obj->left = '17:59';
					$timeDeparted = strtotime( $obj->date.' '.$obj->left );

					// Work out charges for absences 
					// Authorised absence (notified before noon) min 1 hour charge booking fee
					if( substr($obj->left,0,5) == PKB_TIMEOUT_AUTH_ABS )
						$timeDeparted = $timeArrived+1;
					// Absence after noon gets charged at the full rate, until closing time - no matter the start time
					if( substr($obj->left,0,5) == PKB_TIMEOUT_UNAUTH_ABS )
						$timeDeparted = strtotime( $obj->date.' 18:00:00' );

					if( $timeArrived > $timeDeparted )
						throw new PKBASCExceptionBadData('Student '.$student_id.' left before they arrived on '.$obj->date.' ('.date('c', $timeArrived).'/'.date('c',$timeDeparted).')');
					
					// Time at After school, rounded up to nearest 1/2 hour
					$duration = $timeDeparted - $timeArrived;
					$hours    = ceil($duration / (30*60))/2;
					
					$obj->charge = ($obj->hourly_charge * $hours);
					$obj->duration = $hours;
					
					// Minimum charge is 1 hour....
					if($obj->charge < $obj->hourly_charge )
						$obj->charge = $obj->hourly_charge;
					
					$charges[] = $obj;

					// Time after 1800 is charged at quadruple rates!!!!
					if( $obj->left != $original_obj->left )
					{
						$overtime_obj = clone($original_obj);
						
						// Make sure that we only charge double for the excess period
						$overtime_obj->arrived       = '18:00';
						$overtime_obj->hourly_charge = $original_obj->hourly_charge * 4;
						
						$timeArrived  = strtotime( $overtime_obj->date.' '.$overtime_obj->arrived );
						$timeDeparted = strtotime( $overtime_obj->date.' '.$overtime_obj->left );
	
						// Time at After school, rounded up to nearest 1/2 hour
						$duration = $timeDeparted - $timeArrived;
						$hours    = ceil($duration / (30*60))/2;
						
						$overtime_obj->charge   = ($overtime_obj->hourly_charge * $hours);
						$overtime_obj->duration = $hours;
						
						$charges[] = $overtime_obj;
					}
					
				}
			}
			return $charges;
		}

		// Tells you how much the student owes as of "date"		
		public function getBalanceForStudent( $student_id, $startDate, $endDate, $includeInvoicedAttendances=PKB_INVOICE_BOTH )
		{
			set_time_limit( 15 );
			
			/*
			// Soooo... This is where it gets complicated. Hang on tight, you're in for a rough ride...
			
			// WINS/OSCAR (Same thing) payments are paid weekly in arrears to cover the costs of the childcare.
			// *BUT*, they don't pay the full amount that's owed.  They pay $X for Y hours of childcare a week.
			// While Y is fixed (per child, not across all children) and $X is also fixed, $X bears no relation
			// to the amount that PKBASC charges for that childcare hour ($Z). 
			// Y also has no bearing on the number of hours *actually* attended by that child - so sometimes
			// Y will be the actual number of hours that child attended PKBASC, sometimes Y will be WAY over,
			// and sometimes it'll be under.
			// Even when Y matches the real number of hours attended, $X does not equal $Z, because WINS do not
			// pay the amount that PKBASC charges. 
			// Parents are responsible to make up the shortfall between $X & $Z. But... They do not benefit from
			// overpayments, where $X is higher than $Z (i.e child didn't turn up because he was ill/on holiday
			// etc, but PKBASC still got the full Y payment from WINS).
			// To complicate things further, $X is paid weekly, but PKBASC invoices monthly.  $X overpayments
			// do not carry forward between weeks to reduce overall payments within the same invoice.
			
			// So, for the WINS bits, we have to calculate which weeks we received money for, work out the charges
			// for each one of those weeks and calculate the shortfalls that ensued, totalling up to the monthly
			// costs - and for our own interest, we'll record the overpayments that happened too...
			*/			
			
			// Pull all WINS payments made to this student that cover up to this date
			$WINSpayments = self::getWINSPaymentsForStudent( $student_id, date('c', strtotime($startDate)), date('c', strtotime($endDate)) );
			$WINSdiscount = 0;
			$WINSoverpayments = 0;
			foreach( $WINSpayments as $WINS )
			{
				if( isset( $WINS->period_start ) )
				{
					// Now we find out how much costs that student accrued during the period covered by the WINS payment
					$charges = self::getAttendanceChargesForStudent( $student_id, date('c', strtotime($WINS->period_start)), date('c', strtotime($WINS->period_end)),$includeInvoicedAttendances );
					$attendanceChargeForPeriod = 0;
					foreach( $charges as $charge )
						if( isset( $charge->charge ) )
							$attendanceChargeForPeriod += $charge->charge;
					// If the WINS more than covers the costs, then we discount the costs
					if( $WINS->amount >= $attendanceChargeForPeriod )
					{
						$WINSdiscount += $attendanceChargeForPeriod;
						$WINSoverpayments += ( $WINS->amount - $attendanceChargeForPeriod ); // Note: Overpayments are FYI info, not used in costings anywhere
					// Otherwise, if the WINS *doesn't* cover the costs, we only take the WINS payment as the discount amount
					} else $WINSdiscount += $WINS->amount;
				}
			}
			
			// Now work out what the total attendance charges were (we'll discount our WINS payments from this later)
			$attendances = self::getAttendanceChargesForStudent( $student_id,  date('c', strtotime($startDate)), date('c', strtotime($endDate)), $includeInvoicedAttendances );
			$attendancecharges = 0;
			foreach( $attendances as $charge )
				if( isset( $charge->charge ) )
					$attendancecharges += $charge->charge;
			
			// And now we work out how much the parents paid to ASC directly, not via WINS
			$payments = self::getPaymentsForStudent( $student_id, date('c', strtotime($startDate)), date('c', strtotime($endDate)), $includeInvoicedAttendances );
			$totalpaid = 0;
			foreach( $payments as $payment )
				if( isset( $payment->amount ) )
					$totalpaid += $payment->amount;
			
			return array( 
				'CorrectAsOf' => date('c', strtotime($endDate)), 
				'WINSPayments' => $WINSpayments,
				'WINSDiscount' => $WINSdiscount,
				'WINSOverpayments' => $WINSoverpayments,
				'Charges' => $attendances,
				'ChargeTotal' => $attendancecharges,
				'Payments' => $payments,
				'AmountPaid' => $totalpaid,
				'FinalBalance' => ( $totalpaid - ($attendancecharges - $WINSdiscount) )
			);
		}

		// Returns the invoiced balance outstanding for a student as of right now
		public function getCurrentBalanceForStudent( $student_id )
		{
			$invoice = self::getLastInvoiceForStudent( $student_id );
			if( $invoice ) 
				$lastinvoiceddate = strtotime( $invoice->end_date );
			else 	$lastinvoiceddate = 0;
			// Current balances are shown as of the last invoiced date - or never (i.e. we've not invoiced them, they don't know they owe anything)
			return self::getBalanceForStudent( $student_id, date('c', 0), date('c', $lastinvoiceddate) );
		}
		
		// Retrieves the invoice details for the last invoice created for this student
		public function getLastInvoiceForStudent( $student_id )
		{
			$sql = 'SELECT DISTINCT * FROM `invoice` WHERE `student_id` = '.(int)$student_id.' ORDER BY `end_date` DESC LIMIT 1;';
			if( $result = self::$mysqli->query($sql) ) 
				$obj = $result->fetch_object();
			if( !isset($obj) )
				$obj = new stdClass();
			$obj->sql = $sql;
			return $obj;
		}
		
		public function getPaymentsForStudent( $student_id, $startDate, $endDate, $includeInvoicedAttendances=PKB_INVOICE_BOTH )
		{
			$sql = 'SELECT DISTINCT * FROM `payments` WHERE `benefit_payment` = 0 AND `date_received` BETWEEN \''.date('c', strtotime($startDate)).'\' AND \''.date('c', strtotime($endDate)).'\' AND `student_id` = '.(int)$student_id;
			switch( $includeInvoicedAttendances )
			{
				case PKB_INVOICE_INCLUDE:
					$sql .= ' AND `invoiced_id` IS NOT NULL; ';
					break;
				case PKB_INVOICE_EXCLUDE:
					$sql .= ' AND `invoiced_id` IS NULL; ';
					break;
				default:
					break;
			}
			
			$payments = array($sql);
			if( $result = self::$mysqli->query($sql) )
				while ($obj = $result->fetch_object()) 
					$payments[] = $obj;
			return $payments;
		}

		/* Function to work out what students are due to attend during a week, which have bookings, which have known absences and which have requested one-off attendance */
		function getStudentBookings( $weekcommencing )
		{
	
			$date = strtotime($weekcommencing);
			if( date('D', $date) != 'Mon' ) 
				$date = strtotime('next Monday', $date);
			
			// Standard week bookings.
			$query = "
SELECT	DISTINCT
	recurring_booking.mon,
	recurring_booking.tue,
	recurring_booking.wed,
	recurring_booking.thu,
	recurring_booking.fri,
	student.firstname,
	student.lastname,
	student.student_id,
	recurring_booking.start_date,
	recurring_booking.end_date
FROM 	recurring_booking
	INNER JOIN student
		ON recurring_booking.student_id = student.student_id
WHERE	recurring_booking.start_date <= '".date('c', ($date+(4*24*60*60)))."'
	AND recurring_booking.end_date >= '".date('c', $date)."'
ORDER BY recurring_booking.start_date, student.lastname, student.firstname";
	
			$mon = $tue = $wed = $thu = $fri = array();
			if ($result = self::$mysqli->query($query)) 
			{
				while ($obj = $result->fetch_object()) 
				{
					if( $obj->mon && strtotime($obj->start_date) <= ($date+(0*24*60*60)) && strtotime($obj->end_date) >= ($date+(0*24*60*60)) )
						$mon[] = array( BOOKED, $obj->lastname.', '.$obj->firstname );
					if( $obj->tue && strtotime($obj->start_date) <= ($date+(1*24*60*60)) && strtotime($obj->end_date) >= ($date+(1*24*60*60)) ) 
						$tue[] = array( BOOKED, $obj->lastname.', '.$obj->firstname );
					if( $obj->wed && strtotime($obj->start_date) <= ($date+(2*24*60*60)) && strtotime($obj->end_date) >= ($date+(2*24*60*60)) ) 
						$wed[] = array( BOOKED, $obj->lastname.', '.$obj->firstname );
					if( $obj->thu && strtotime($obj->start_date) <= ($date+(3*24*60*60)) && strtotime($obj->end_date) >= ($date+(4*24*60*60)) ) 
						$thu[] = array( BOOKED, $obj->lastname.', '.$obj->firstname );
					if( $obj->fri && strtotime($obj->start_date) <= ($date+(4*24*60*60)) && strtotime($obj->end_date) >= ($date+(4*24*60*60)) ) 
						$fri[] = array( BOOKED, $obj->lastname.', '.$obj->firstname );
				}
				/* free result set */
				$result->close();
			}
	
			// Find if we have any exceptions
			$exceptionquery = "
SELECT	DISTINCT
	student.student_id,
	student.firstname,
	student.lastname,
	'Mon' AS day,
	reason
FROM	booking_exception
	INNER JOIN student
		ON student.student_id = booking_exception.student_id
WHERE	`date` = '".date('c', $date)."'
	
UNION
SELECT	DISTINCT
	student.student_id,
	student.firstname,
	student.lastname,
	'Tue' AS day,
	reason
FROM	booking_exception
	INNER JOIN student
		ON student.student_id = booking_exception.student_id
WHERE	`date` = '".date('c', ($date+(1*24*60*60)))."'

UNION
SELECT	DISTINCT
	student.student_id,
	student.firstname,
	student.lastname,
	'Wed' AS day,
	reason
FROM	booking_exception
	INNER JOIN student
		ON student.student_id = booking_exception.student_id
WHERE	`date` = '".date('c', ($date+(2*24*60*60)))."'

UNION
SELECT	DISTINCT
	student.student_id,
	student.firstname,
	student.lastname,
	'Thu' AS day,
	reason
FROM	booking_exception
	INNER JOIN student
		ON student.student_id = booking_exception.student_id
WHERE	`date` = '".date('c', ($date+(3*24*60*60)))."'

UNION
SELECT	DISTINCT
	student.student_id,
	student.firstname,
	student.lastname,
	'Fri' AS day,
	reason
FROM	booking_exception
	INNER JOIN student
		ON student.student_id = booking_exception.student_id
WHERE	`date` = '".date('c', ($date+(4*24*60*60)))."'";
	
			if( $exceptionresult =  self::$mysqli->query($exceptionquery)) 
			{
				while ($obj = $exceptionresult->fetch_object()) 
				{
					if( $obj->day == 'Mon' )
					{
						$found = false;
						for( $i=0; $i<count($mon); $i++ )
						{
							if( $mon[$i] == array( BOOKED, $obj->lastname.', '.$obj->firstname ) )
							{
								$mon[$i] = array( ABSENT, $obj->lastname.', '.$obj->firstname, $obj->reason );
								$found = true;	
								break;
							}
						}
						if( !$found ) $mon[$i] = array( REQUEST, $obj->lastname.', '.$obj->firstname, $obj->reason );
					} elseif( $obj->day == 'Tue' ) {
						$found = false;
						for( $i=0; $i<count($tue); $i++ )
						{
							if( $tue[$i] == array( BOOKED, $obj->lastname.', '.$obj->firstname ) )
							{
								$tue[$i] = array( ABSENT, $obj->lastname.', '.$obj->firstname, $obj->reason );
								$found = true;	
								break;
							}
						}
						if( !$found ) $tue[$i] = array( REQUEST, $obj->lastname.', '.$obj->firstname, $obj->reason );
					} elseif( $obj->day == 'Wed' ) {
						$found = false;
						for( $i=0; $i<count($wed); $i++ )
						{
							if( $wed[$i] == array( BOOKED, $obj->lastname.', '.$obj->firstname ) )
							{
								$wed[$i] = array( ABSENT, $obj->lastname.', '.$obj->firstname, $obj->reason );
								$found = true;	
								break;
							}
						}
						if( !$found ) $wed[$i] = array( REQUEST, $obj->lastname.', '.$obj->firstname, $obj->reason );
					} elseif( $obj->day == 'Thu' ) {
						$found = false;
						for( $i=0; $i<count($thu); $i++ )
						{
							if( $thu[$i] == array( BOOKED, $obj->lastname.', '.$obj->firstname ) )
							{
								$thu[$i] = array( ABSENT, $obj->lastname.', '.$obj->firstname, $obj->reason );
								$found = true;	
								break;
							}
						}
						if( !$found ) $thu[$i] = array( REQUEST, $obj->lastname.', '.$obj->firstname, $obj->reason );
					} else {
						$found = false;
						for( $i=0; $i<count($fri); $i++ )
						{
							if( $fri[$i] == array( BOOKED, $obj->lastname.', '.$obj->firstname ) )
							{
								$fri[$i] = array( ABSENT, $obj->lastname.', '.$obj->firstname, $obj->reason );
								$found = true;	
								break;
							}
						}
						if( !$found ) $fri[$i] = array( REQUEST, $obj->lastname.', '.$obj->firstname, $obj->reason );
					}	
				}
				$exceptionresult->close();
			}
	
			return array( $mon, $tue, $wed, $thu, $fri );
		}
		
		public function getStudentDetails( $student_id )
		{
			$query = "SELECT DISTINCT * FROM `student` WHERE `student_id` = ".(int)$student_id." LIMIT 1;";
			
			if ($result = self::$mysqli->query($query)) 
				return $result->fetch_object();
		}

		public function getStudentList()
		{
			$query = "SELECT DISTINCT student_id, firstname, lastname, balance, `wins_overpayments`, `display` FROM student ORDER BY lastname, firstname, lastname";
			$students = array($query);	
			if ($result = self::$mysqli->query($query)) 
				while ($obj = $result->fetch_object())
					$students[] = $obj;
			return $students;
		}

		// Extract WINS payments that cover the period requested
		public function getWINSPaymentsForStudent( $student_id, $startDate, $endDate )
		{
			$sql = 'SELECT DISTINCT * FROM `payments` WHERE `benefit_payment` = 1 AND `period_end` BETWEEN \''.date('c', strtotime($startDate)).'\' AND \''.date('c', strtotime($endDate)).'\' AND `student_id` = '.(int)$student_id.';';
			$payments = array($sql);
			if( $result = self::$mysqli->query($sql) )
				while ($obj = $result->fetch_object()) 
					$payments[] = $obj;
			return $payments;
		}
		
		public function createInvoice()
		{
			$query = '
INSERT INTO invoice (
	student_id,
	reference,
	start_date,
	end_date,
	amount_billed
) VALUES (
	0,
	\'\',
	\'1970-01-01\',
	\'1970-01-01\',
	0
)';
			self::$mysqli->query($query);
			return self::$mysqli->insert_id;
		}		
	
		public function saveInvoiceDetails( $invoice_id, $student_id, $reference, $start_date, $end_date, $details, $PDFtext )
		{
			//$endbalance = self::getBalanceForStudent( $student_id, date('c', 0), date('c', strtotime($details['CorrectAsOf'])) );

			$query = '
UPDATE	invoice
SET	student_id = '.(int)$student_id.',
	reference = \''.$reference.'\',
	start_date = \''.date('Y-m-d', strtotime(str_replace(',','',$start_date))).'\',
	end_date = \''.date('Y-m-d', strtotime(str_replace(',','',$end_date))).'\',
	amount_billed = '.($details[0]['FinalBalance']+$details[1]['FinalBalance']).'
WHERE	invoice_id = '.(int)$invoice_id.'
LIMIT 1;';
			self::$mysqli->query($query);

			$query = 'UPDATE `student` SET `balance` = `balance`+'.($details[0]['FinalBalance']+$details[1]['FinalBalance']).', `wins_overpayments` = `balance`+'.$details[1]['WINSOverpayments'].' WHERE `student_id` = '.(int)$student_id.' LIMIT 1;';
			self::$mysqli->query($query);
			
			$attendancesinvoiced = array();
			foreach( $details[1]['Charges'] as $charge )
				if( isset( $charge->charge ) )
					$attendancesinvoiced[] = $charge->attendance_id;
			
			if( count($attendancesinvoiced) )
			{
				$query = 'UPDATE `attendance` SET `invoiced_id` = '.$invoice_id.' WHERE `attendance_id` IN ('.implode(',',$attendancesinvoiced).');';
				self::$mysqli->query($query);
			}
			
			$paymentsinvoiced = array();
			foreach( $details[1]['Payments'] as $charge )
				if( isset( $charge->payment_id ) )
					$paymentsinvoiced[] = $charge->payment_id;
			
			if( count($paymentsinvoiced) )
			{
				$query = 'UPDATE `payments` SET `invoiced_id` = '.$invoice_id.' WHERE `payment_id` IN ('.implode(',',$paymentsinvoiced).');';
				self::$mysqli->query($query);
			}

			$fp = fopen('invoices/'.$invoice_id.'.pdf', 'w');
			fwrite($fp, $PDFtext);
			fclose($fp);
			
			return $invoice_id;
		}
		
		
		public function _MismatchesInSystem()
		{
			$sql = 'SELECT DISTINCT * FROM `student` ORDER BY `lastname`,`firstname`;';
			if( $result = self::$mysqli->query($sql) )
			{
				while ($obj = $result->fetch_object())
				{
					$foo = $this->getCurrentBalanceForStudent( $obj->student_id );
					if( (0-$foo['FinalBalance']) != $obj->balance )
					{
						var_dump( '********************************');
						var_dump( '<strong>'.$obj->lastname.', '.$obj->firstname.'</strong>');
						var_dump( '  System believes:   '.$obj->balance );
						var_dump('');
						var_dump( '  System count:      '.(0-$foo['FinalBalance']) );
						foreach( $foo['Charges'] as $charge )
							var_dump( '    '.$charge->date.' > $'.$charge->charge );
						var_dump( '                 -------' );
						var_dump( '    Owes:        $'.$foo['ChargeTotal'] );
						foreach( $foo['Payments'] as $charge )
							var_dump( '    '.$charge->date_received.' > $'.$charge->amount.' '.$charge->reference );
						var_dump( '                 -------' );
						var_dump( '    Paid:        $'.$foo['AmountPaid'] );
						$foo = $this->getBalanceForStudent( $obj->student_id, date('c') );
						var_dump('');
						var_dump( '  Balance right now: '.(0-$foo['FinalBalance']) );
						foreach( $foo['Charges'] as $charge )
							var_dump( '    '.$charge->date.' > $'.$charge->charge );
						var_dump( '                 -------' );
						var_dump( '    Owes:        $'.$foo['ChargeTotal'] );
						foreach( $foo['Payments'] as $charge )
							var_dump( '    '.$charge->date_received.' > $'.$charge->amount.' '.$charge->reference );
						var_dump( '                 -------' );
						var_dump( '    Paid:        $'.$foo['AmountPaid'] );
						var_dump('');
					}
				}
			}
		}
		
		
	}
?>

