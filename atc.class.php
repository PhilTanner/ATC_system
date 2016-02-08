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
	define( 'ATC_USER_LEVEL_ADMIN', 							16777215 );
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
	class ATCExceptionBadData extends ATCException {}
	class ATCExceptionDBConn extends ATCException {}
	class ATCExceptionDBError extends ATCExceptionDBConn {}
	class ATCExceptionInsufficientPermissions extends ATCException {}
	
	
	class ATC
	{
		protected static $mysqli;
		protected static $currentuser;
		public static $dbUpToDate = false;
		
		public function __construct()
		{
			self::$mysqli = new mysqli(DB_HOST, DB_USER, DB_PSWD, DB_NAME);
			/* check connection */
			if (mysqli_connect_errno())
			    throw new ATCExceptionnDBConn(mysqli_connect_error());
			if(ATC_DEBUG) self::$currentuser = 1;
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
				
		public function get_personnel( $id, $orderby = "ASC" )
		{
			$personnel = new stdClass();

			if(!self::user_has_permission( ATC_USER_PERMISSION_PERSONNEL_VIEW, $id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this user");
				
			switch( $id )
			{
				// only loose casting, so work it out properly here
				case null:
				case 0:
					if( is_null($id) )
					{
						$personnel = array();
						$query = "SELECT * FROM `personnel`  ORDER BY `enabled` ASC, `lastname` ".htmlentities($orderby).", `firstname` ".htmlentities($orderby).", `personnel_id` ".htmlentities($orderby).";";
						
						if ($result = self::$mysqli->query($query))
						{
							while ( $obj = $result->fetch_object() )
								$personnel[] = $obj;
							foreach( $personnel as $obj )
								$obj->rank = "";
						}	
						else
							throw new ATCExceptionDBError(self::$mysqli->error);
					} else {
						$personnel->personnel_id = 0;
						$personnel->firstname = null;
						$personnel->lastname = null;
						$personnel->email = null;
						$personnel->access_rights = 0;
						$personnel->joined_date = null;
						$personnel->left_date = null;
						$personnel->is_female = 0;
						$personnel->dob = null;
						$personnel->rank = null;
						$personnel->enabled = -1;
						$personnel->created = date("d/m/Y h:i a", time());
					}
					break;
				default:
					$query = "SELECT * FROM `personnel` WHERE `personnel_id` = ".(int)$id." LIMIT 1;";
					
					if ($result = self::$mysqli->query($query)) 
						$personnel = $result->fetch_object();
					else
						throw new ATCExceptionDBError(self::$mysqli->error);
					$personnel->created = date("Y-m-d\TH:i", strtotime($personnel->created));
					$personnel->rank = null;
					
					break;
			}
			return $personnel;
		}
		
		// Keep a track of who's doing what, for later auditing.
		private function log_action( $table_name, $sql_run )
		{
			$query = "INSERT INTO `log_changes` (`personnel_id`, `sql_executed`, `table_updated` ) VALUES ( ".self::$currentuser.', "'.htmlentities($sql_run).'", "'.htmlentities($table_name).'" );';
			if ($result = self::$mysqli->query($query))	return true;
			else throw new ATCExceptionDBError(self::$mysqli->error);
		}
		
		public function set_personnel( &$user )
		{
			$query = "";
			if(!self::user_has_permission( ATC_USER_PERMISSION_PERSONNEL_EDIT, $user->personnel_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this user");
				
			if( !$user->personnel_id )
			{
				$query .= "INSERT INTO `personnel` (`firstname`, `lastname`, `email`, `dob`, `password`, `joined_date`, `left_date`, `access_rights`, `is_female`, `enabled` ) VALUES ( ";
				$query .= '"'.htmlentities($user->firstname).'", "'.htmlentities($user->lastname).'", "'.htmlentities($user->email).'", "'.date('Y-m-d',strtotime($user->dob)).'", ';
				$query .= '"'.htmlentities(create_hash($user->password)).'", "'.date('Y-m-d',strtotime($user->joined_date)).'", '.(strtotime($user->left_date)?'"'.date('Y-m-d',strtotime($user->left_date)).'"':'NULL').', '.(int)$user->access_rights.', ';
				$query .= (int)$user->is_female.', '.(isset($user->enabled)&&$user->enabled==-1?-1:0).' );';
				if ($result = self::$mysqli->query($query))
				{
					$user->personnel_id = self::$mysqli->insert_id;
					self::log_action( 'personnel', $query );
					return true;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query .= 'UPDATE `personnel` SET `firstname` = "'.htmlentities($user->firstname).'", `lastname` = "'.htmlentities($user->lastname).'", `email` = "'.htmlentities($user->email).'", `dob` = "'.date('Y-m-d',strtotime($user->dob)).'", ';
				if( strlen(trim($user->password)) ) 
					 $query .= '`password` = "'.htmlentities(create_hash($user->password)).'", ';
				$query .= '`joined_date` = "'.date('Y-m-d',strtotime($user->joined_date)).'", ';
				if( strtotime($user->left_date) )
					$query .= '`left_date` = "'.date('Y-m-d',strtotime($user->left_date)).'", ';
				else 
					$query .= '`left_date` = NULL, ';
				$query .= '`access_rights` = '.(int)$user->access_rights.', `enabled` = '.(isset($user->enabled)&&$user->enabled==-1?-1:0).', `is_female` = '.(int)$user->is_female;
				$query .= ' WHERE personnel_id = '.(int)$user->personnel_id.' LIMIT 1;';

				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'personnel', $query );
					return true;
				} else
					throw new ATCExceptionDBError(self::$mysqli->error);
				
			}
			return false;
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
				$(".navoptions ul li a").button().addClass("ui-state-disabled");
				$(".navoptions ul li a.home").button({ icons: { primary: "ui-icon-home" } }).removeClass("ui-state-disabled")'.($title=='Home'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.personnel").button({ icons: { primary: "ui-icon-person" } }).removeClass("ui-state-disabled")'.($title=='Personnel'?'.addClass("ui-state-active")':'').';
			});
			
		</script>
		
	</head>
	<body>
		<div id="dialog"></div>
		<nav class="navoptions">
			<ul>
				<li> <a href="./" class="home">Home</a> </li>
				<li> <a href="./personnel.php" class="personnel">Personnel</a> </li>
				<li> <a href="./" class="attendance">Attendance</a> </li>
				<li> <a href="./" class="activities">Activities</a> </li>
				<li> <a href="./" class="finance">Finance</a> </li>
				<li> <a href="./" class="stores">Stores</a> </li>
				<li> <a href="./" class="training">Training</a> </li>
			</ul>
		</nav>
		<h1> ATC '.$title.' </h1>
';
		}
		
		public function user_has_permission( $permission, $target )
		{
			if(ATC_DEBUG) return true;
		}
		
		
	}
?>

