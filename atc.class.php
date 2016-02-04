<?php
	require_once 'config.php';
	
	class ATCException extends Exception {
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
	class ATCExceptionnBadData extends ATCExceptionn {}
	class ATCExceptionnDBConn extends ATCExceptionn {}
	
	define( 'PKB_INVOICE_INCLUDE', 0 );
	define( 'PKB_INVOICE_EXCLUDE', 1 );
	define( 'PKB_INVOICE_BOTH',    2 );
	
	define( 'PKB_TIMEOUT_AUTH_ABS',  '15:10' );
	define( 'PKB_TIMEOUT_UNAUTH_ABS','15:00' );

	define( 'ATC_DEBUG', 				1 );

	define( 'ATC_USER_LEVEL_ADMIN', 	1 );
	define( 'ATC_USER_LEVEL_CADET', 	2 );
	define( 'ATC_USER_LEVEL_JNCO', 		4 );
	define( 'ATC_USER_LEVEL_SNCO', 		8 );
	define( 'ATC_USER_LEVEL_OFFICER', 	16 );
	define( 'ATC_USER_LEVEL_ADJUTANT', 	32 );
	define( 'ATC_USER_LEVEL_STORES', 	64 );
	define( 'ATC_USER_LEVEL_TRAINING', 	128 );
	define( 'ATC_USER_LEVEL_CUCDR', 	512 );
	define( 'ATC_USER_LEVEL_SUPOFF', 	1024 );
	define( 'ATC_USER_LEVEL_TREASURER',	2048 );
	//define( 'ATC_USER_LEVEL_ADMIN', 	4096 );

	
	class ATC
	{
		protected static $mysqli;
		public static $dbUpToDate = false;
		
		public function __construct()
		{
			if(ATC_DEBUG) recordTimestamp('PKBASC starting');
			self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
			if(ATC_DEBUG) recordTimestamp('mysqli initiated');
			/* check connection */
			if (mysqli_connect_errno())
			    throw new ATCExceptionnDBConn(mysqli_connect_error());

/*
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
*/
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
		
		
		
	}
?>

