<?php
	define( 'ATC_DEBUG', 										1 );

	define( 'ATC_USER_PERMISSION_PERSONNEL_VIEW', 				1 );
	define( 'ATC_USER_PERMISSION_PERSONNEL_EDIT',		 		ATC_USER_PERMISSION_PERSONNEL_VIEW + 2 );
	define( 'ATC_USER_PERMISSION_ATTENDANCE_VIEW', 				4 );
	define( 'ATC_USER_PERMISSION_ATTENDANCE_EDIT', 				ATC_USER_PERMISSION_ATTENDANCE_VIEW + 8 );
	define( 'ATC_USER_PERMISSION_ACTIVITIES_VIEW', 				16 );
	define( 'ATC_USER_PERMISSION_ACTIVITIES_EDIT', 				ATC_USER_PERMISSION_ACTIVITIES_VIEW + 32 );
	define( 'ATC_USER_PERMISSION_FINANCE_VIEW', 				64 );
	define( 'ATC_USER_PERMISSION_FINANCE_EDIT', 				ATC_USER_PERMISSION_FINANCE_VIEW + 128 );
	define( 'ATC_SYSTEM_PERMISSION_VIEW', 						512 );
	define( 'ATC_SYSTEM_PERMISSION_EDIT', 						ATC_SYSTEM_PERMISSION_VIEW + 1024 );
	define( 'ATC_USER_PERMISSION_STORES_VIEW',					2048 );
	define( 'ATC_USER_PERMISSION_STORES_EDIT',					ATC_USER_PERMISSION_STORES_VIEW + 4096 );
	define( 'ATC_STORES_PERMISSION_VIEW',						8192 );
	define( 'ATC_STORES_PERMISSION_EDIT',						ATC_STORES_PERMISSION_VIEW + 16384 );
	define( 'ATC_ACTIVITIES_PERMISSION_VIEW',					32768 );
	define( 'ATC_ACTIVITIES_PERMISSION_EDIT',					ATC_ACTIVITIES_PERMISSION_VIEW + 65536 );

	// Give admin everything we can think of in the future.
	define( 'ATC_USER_LEVEL_ADMIN', 							1048575 );
	define( 'ATC_USER_LEVEL_CADET', 							0 );
	define( 'ATC_USER_LEVEL_NCO', 								ATC_USER_PERMISSION_PERSONNEL_VIEW );
	//define( 'ATC_USER_LEVEL_OFFICER', 							ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_ACTIVITIES_VIEW );
	define( 'ATC_USER_LEVEL_ADJUTANT', 							ATC_USER_PERMISSION_PERSONNEL_EDIT + ATC_USER_PERMISSION_ATTENDANCE_EDIT + ATC_USER_PERMISSION_ACTIVITIES_EDIT + ATC_USER_PERMISSION_FINANCE_EDIT + ATC_USER_PERMISSION_STORES_VIEW + ATC_ACTIVITIES_PERMISSION_EDIT );
	define( 'ATC_USER_LEVEL_STORES', 							ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_FINANCE_EDIT + ATC_USER_PERMISSION_STORES_EDIT );
	define( 'ATC_USER_LEVEL_TRAINING', 							ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_FINANCE_VIEW + ATC_USER_PERMISSION_STORES_VIEW + ATC_ACTIVITIES_PERMISSION_EDIT );
	define( 'ATC_USER_LEVEL_CUCDR', 							ATC_USER_PERMISSION_PERSONNEL_EDIT + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_ACTIVITIES_VIEW + ATC_USER_PERMISSION_FINANCE_VIEW + ATC_USER_PERMISSION_STORES_VIEW );
	define( 'ATC_USER_LEVEL_SUPOFF', 							ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_ACTIVITIES_VIEW );
	define( 'ATC_USER_LEVEL_TREASURER',							ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_ACTIVITIES_VIEW + ATC_USER_PERMISSION_STORES_VIEW + ATC_USER_PERMISSION_FINANCE_EDIT );
	define( 'ATC_USER_LEVEL_USC', 								ATC_USER_PERMISSION_PERSONNEL_VIEW + ATC_USER_PERMISSION_ATTENDANCE_VIEW + ATC_USER_PERMISSION_ACTIVITIES_VIEW + ATC_USER_PERMISSION_STORES_VIEW + ATC_USER_PERMISSION_FINANCE_VIEW );

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
	class ATCExceptionnBadData extends ATCException {}
	class ATCExceptionnDBConn extends ATCException {}
	
	
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
				
		public function get_personnel( $id )
		{
			$personnel = new stdClass();

			switch( $id )
			{
					// only loose casting, so work it out properly here
					case null:
					case 0:
						if( is_null($id) )
						{
							$personnel = array();
							$query = "SELECT * FROM `personnel`;";
							
							if ($result = self::$mysqli->query($query))
								while ( $obj = $result->fetch_object() )
									$personnel[] = $obj;
						} else {
							$personnel->personnel_id = 0;
							$personnel->firstname = null;
							$personnel->lastname = null;
							$personnel->email = null;
							$personnel->access_rights = 2;
							$personnel->created = date("d/m/Y h:i a", time());
						}
						break;
					default:
						$query = "SELECT * FROM `personnel` WHERE `personnel_id` = ".(int)$id." LIMIT 1;";
						
						if ($result = self::$mysqli->query($query)) 
							$personnel = $result->fetch_object();
						$personnel->created = date("Y-m-d\TH:i", strtotime($personnel->created));

						break;
			}
			return $personnel;
		}	
		
		public function set_personnel( &$user )
		{
			$query = "";
			if( !$user->personnel_id )
			{
				$query .= "INSERT INTO `personnel` (`firstname`, `lastname`, `email`, `dob`, `password`, `access_rights` ) VALUES ( ";
				$query .= '"'.htmlentities($user->firstname).'", "'.htmlentities($user->lastname).'", "'.htmlentities($user->email).'", "'.date('Y-m-d',strtotime($user->dob)).'", "'.htmlentities(create_hash($user->password)).'", '.(int)$user->access_rights.' );';
				if ($result = self::$mysqli->query($query))
				{
					$user->personnel_id = self::$mysqli->insert_id;
					return true;
				}
				return false;
			} else {
				$query .= 'UPDATE `personnel` SET `firstname` = "'.htmlentities($user->firstname).'", `lastname` = "'.htmlentities($user->lastname).'", `email` = "'.htmlentities($user->email).'", `dob` = "'.date('Y-m-d',strtotime($user->dob)).'", ';
				if( strlen(trim($user->password)) ) 
					 $query .= '`password` = "'.htmlentities(create_hash($user->password)).'", ';
				$query .= '`access_rights` = '.(int)$user->access_rights.' WHERE personnel_id = '.(int)$user->personnel_id.' LIMIT 1;';
				if ($result = self::$mysqli->query($query))
					return true;
				return false;
			}
		}	
		
		public function gui_output_page_footer( $title )
		{
			echo '
	</body>
</html>';
		}
		
		public function gui_output_page_header( $title )
		{
			echo '<!doctype html>
<html lang="us">
	<head>
		<meta charset="utf-8">
		<title>ATC '.$title.'</title>
		<link href="jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" rel="stylesheet">
		<link href="atc.css" rel="stylesheet">
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-1.8.3.js"></script>
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
		<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
		
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		
		<script type="text/javascript">
			$(function(){
			});
			
		</script>
		
	</head>
	<body>
		<div class="navoptions">
			<button class="home" type="button">Home</button><br />
		</div>
		<h1> ATC '.$title.' </h1>
		<div id="dialog"></div>
';
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

