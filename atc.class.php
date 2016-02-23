<?php
	define( 'ATC_DEBUG', 					1 );
	define( 'ATC_SETTING_PARADE_NIGHT',			"Wednesday" );
	define( 'ATC_SETTING_DATETIME_INPUT',         "Y-m-d\TH:i");
	define( 'ATC_SETTING_FULL_DISPLAY_NAME',		'CONCAT("RNK, ", `personnel`.`lastname`,", ",`personnel`.`firstname`)' );
	define( 'ATC_SETTING_DISPLAY_NAME',		'CONCAT(`personnel`.`lastname`,", ",`personnel`.`firstname`)' );

	// Permissions structure, as a bitmask
	define( 'ATC_PERMISSION_PERSONNEL_VIEW', 		1 );
	define( 'ATC_PERMISSION_PERSONNEL_EDIT',		ATC_PERMISSION_PERSONNEL_VIEW + 2 );
	define( 'ATC_PERMISSION_ATTENDANCE_VIEW', 		4 );
	define( 'ATC_PERMISSION_ATTENDANCE_EDIT', 		ATC_PERMISSION_ATTENDANCE_VIEW + 8 );
	define( 'ATC_PERMISSION_ACTIVITIES_VIEW', 		16 );
	define( 'ATC_PERMISSION_ACTIVITIES_EDIT', 		ATC_PERMISSION_ACTIVITIES_VIEW + 32 );
	define( 'ATC_PERMISSION_FINANCE_VIEW', 			64 );
	define( 'ATC_PERMISSION_FINANCE_EDIT', 			ATC_PERMISSION_FINANCE_VIEW + 128 );
	define( 'ATC_PERMISSION_SYSTEM_VIEW', 			512 );
	define( 'ATC_PERMISSION_SYSTEM_EDIT', 			ATC_PERMISSION_SYSTEM_VIEW + 1024 );
	define( 'ATC_PERMISSION_STORES_VIEW',			2048 );
	define( 'ATC_PERMISSION_STORES_EDIT',			ATC_PERMISSION_STORES_VIEW + 4096 );
	define( 'ATC_PERMISSION_LOCATIONS_VIEW',		8192 );
	define( 'ATC_PERMISSION_LOCATIONS_EDIT',		ATC_PERMISSION_LOCATIONS_VIEW + 16384 );
	define( 'ATC_PERMISSION_ACTIVITY_TYPE_EDIT',		32768 );

	// Give admin everything we can think of in the future.
	define( 'ATC_USER_LEVEL_ADMIN', 			16777215 );
	define( 'ATC_USER_LEVEL_CADET', 			0 );
	define( 'ATC_USER_LEVEL_NCO', 				ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_ADJUTANT', 			ATC_PERMISSION_PERSONNEL_EDIT + ATC_PERMISSION_ATTENDANCE_EDIT + ATC_PERMISSION_ACTIVITIES_EDIT + ATC_PERMISSION_FINANCE_EDIT + ATC_PERMISSION_STORES_VIEW + ATC_PERMISSION_LOCATIONS_EDIT + ATC_PERMISSION_ACTIVITY_TYPE_EDIT);
	define( 'ATC_USER_LEVEL_STORES', 			ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_FINANCE_EDIT + ATC_PERMISSION_STORES_EDIT + ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_TRAINING', 			ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_ATTENDANCE_VIEW + ATC_PERMISSION_FINANCE_VIEW + ATC_PERMISSION_STORES_VIEW + ATC_PERMISSION_LOCATIONS_EDIT + ATC_PERMISSION_ACTIVITY_TYPE_EDIT );
	define( 'ATC_USER_LEVEL_CUCDR', 			ATC_PERMISSION_PERSONNEL_EDIT + ATC_PERMISSION_ATTENDANCE_VIEW + ATC_PERMISSION_ACTIVITIES_VIEW + ATC_PERMISSION_FINANCE_VIEW + ATC_PERMISSION_STORES_VIEW + ATC_PERMISSION_LOCATIONS_VIEW + ATC_PERMISSION_ACTIVITY_TYPE_EDIT );
	define( 'ATC_USER_LEVEL_SUPOFF', 			ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_ATTENDANCE_VIEW + ATC_PERMISSION_ACTIVITIES_VIEW + ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_TREASURER',			ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_ATTENDANCE_VIEW + ATC_PERMISSION_ACTIVITIES_VIEW + ATC_PERMISSION_STORES_VIEW + ATC_PERMISSION_FINANCE_EDIT + ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_USC', 				ATC_PERMISSION_PERSONNEL_VIEW + ATC_PERMISSION_ATTENDANCE_VIEW + ATC_PERMISSION_ACTIVITIES_VIEW + ATC_PERMISSION_STORES_VIEW + ATC_PERMISSION_FINANCE_VIEW + ATC_PERMISSION_LOCATIONS_VIEW );

	define( 'ATC_USER_GROUP_OFFICERS',			ATC_USER_LEVEL_ADJUTANT.','.ATC_USER_LEVEL_STORES.','.ATC_USER_LEVEL_TRAINING.','.ATC_USER_LEVEL_CUCDR.','.ATC_USER_LEVEL_SUPOFF );
	define( 'ATC_USER_GROUP_CADETS',			ATC_USER_LEVEL_CADET.','.ATC_USER_LEVEL_NCO );
	define( 'ATC_USER_GROUP_PERSONNEL',			ATC_USER_GROUP_OFFICERS.','.ATC_USER_GROUP_CADETS );

	define( 'ATC_ATTENDANCE_PRESENT',			0 );
	define( 'ATC_ATTENDANCE_ON_LEAVE',			1 );
	define( 'ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE',		2 );
	define( 'ATC_ATTENDANCE_PRESENT_SYMBOL',		"X" );
	define( 'ATC_ATTENDANCE_ON_LEAVE_SYMBOL',		"L" );
	define( 'ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE_SYMBOL',	"o" );

	define( 'ATC_ACTIVITY_RECOGNISED',			0 );
	define( 'ATC_ACTIVITY_AUTHORISED',			1 );

	define( 'ATC_DRESS_CODE_BLUES',				0 );
	define( 'ATC_DRESS_CODE_DPM',				1 );
	define( 'ATC_DRESS_CODE_BLUES_AND_DPM',			2 );

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
			return sprintf("EXCEPTION:PKBASC:%s", $class, self::$mysqli->real_escape_string($this->getMessage()));
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
			    throw new ATCExceptionDBConn(mysqli_connect_error());
			if(ATC_DEBUG) self::$currentuser = 1;
		}
		
		public function add_parade_night( $date )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ATTENDANCE_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = "INSERT INTO `attendance` (`date` ) VALUES ( '".date("Y-m-d",$date)."' );";
			if ($result = self::$mysqli->query($query))
			{
				self::log_action( 'attendance', $query, self::$mysqli->insert_id );
				return true;
			}
			else throw new ATCExceptionDBError(self::$mysqli->error);
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
				
		public function get_activities( $date=null, $days=365 )
		{
			if( is_null($date) ) $startdate = time()-(14*24*60*60);
			else $startdate = strtotime($date);
			$enddate = $startdate + ((int)$days*24*60*60);
			
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = '
				SELECT	`activity`.*,
					`activity_type`.*,
					`personnel`.`firstname`,
					`personnel`.`lastname`,
					" " AS `rank`,
					(
						SELECT	COUNT(`personnel`.`personnel_id`)
						FROM	`activity_register`
							INNER JOIN `personnel`
								ON `personnel`.`personnel_id` = `activity_register`.`personnel_id`
						WHERE	`activity_register`.`activity_id` = `activity`.`activity_id`
							AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_OFFICERS.')
					) AS `officers_attending`,
					(
						SELECT	COUNT(`personnel`.`personnel_id`)
						FROM	`activity_register`
							INNER JOIN `personnel`
								ON `personnel`.`personnel_id` = `activity_register`.`personnel_id`
						WHERE	`activity_register`.`activity_id` = `activity`.`activity_id`
							AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_CADETS.')
					) AS `cadets_attending`
				FROM 	`activity` 
					INNER JOIN `activity_type`
						ON `activity`.`activity_type_id` = `activity_type`.`activity_type_id`
					INNER JOIN `personnel`
						ON `activity`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 	`activity`.`startdate` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'" 
					AND `activity`.`activity_id` > 0
				ORDER BY `startdate` ASC;';

			$activities = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
				{
					$activities[] = $obj;
				}
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			
			return $activities;
		}
		
		public function get_activity( $id )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = '
				SELECT	`activity`.*,
					`activity_type`.*,
					`location`.*,
					`personnel`.`firstname`,
					`personnel`.`lastname`,
					" " AS `rank`,
					(
						SELECT	GROUP_CONCAT(DISTINCT `personnel_id` SEPARATOR ",")
						FROM 	`activity_register`
						GROUP BY `activity_id`
						HAVING	`activity_id` = `activity`.`activity_id`
					) AS `attendees`
				FROM 	`activity` 
					INNER JOIN `activity_type`
						ON `activity`.`activity_type_id` = `activity_type`.`activity_type_id`
					INNER JOIN `personnel`
						ON `activity`.`personnel_id` = `personnel`.`personnel_id`
					INNER JOIN `location`
						ON `activity`.`location_id` = `location`.`location_id`
				WHERE 	`activity`.`activity_id` = '.(int)$id.' 
				LIMIT 1;';

			$activities = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
				{
					$obj->startdate = date(ATC_SETTING_DATETIME_INPUT, strtotime($obj->startdate));
					$obj->enddate = date(ATC_SETTING_DATETIME_INPUT, strtotime($obj->enddate));
					$obj->attendees = explode(',', $obj->attendees);
					$activities[] = $obj;
				}
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $activities;
		}
		
		public function get_activity_attendance( $id )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			if( !self::user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
				SELECT	`activity_register`.*,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					" " AS rank
				FROM 	`activity_register`
					INNER JOIN `personnel`
						ON `activity_register`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 	`activity_register`.`activity_id` = '.(int)$id.'
				ORDER BY `personnel`.`lastname`, `personnel`.`firstname`;';

			$attendees = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
				{
					if(!is_null($obj->presence))
						$obj->presence = (int)$obj->presence;
					$attendees[] = $obj;
				}
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $attendees;
		}
		
		public function get_activity_names()
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'SELECT DISTINCT `title` FROM 	`activity` WHERE `activity`.`activity_id` > 0 ORDER BY LOWER(`title`) ASC;';

			$activities = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$activities[] = $obj->title;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return $activities;
		}
		
		public function get_activity_types()
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'SELECT * FROM `activity_type` ORDER BY LOWER(`type`) ASC;';

			$activities = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$activities[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return $activities;
		}
		
		public function get_attendance( $startdate, $enddate )
		{
			$startdate = strtotime($startdate);
			$enddate = strtotime($enddate);

			if(!self::user_has_permission( ATC_PERMISSION_ATTENDANCE_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'SELECT * FROM `attendance` WHERE `date` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'" ORDER BY `date` ASC;';
			
			$dates = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
					$dates[] = $obj;
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $dates;
		}
		
		public function get_attendance_register( $startdate, $enddate )
		{
			$startdate = strtotime($startdate);
			$enddate = strtotime($enddate);

			if(!self::user_has_permission( ATC_PERMISSION_ATTENDANCE_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = '
			SELECT	`attendance_register`.*,
				`personnel`.`access_rights`
			FROM 	`attendance_register` 
				INNER JOIN `personnel` 
					ON `attendance_register`.`personnel_id` = `personnel`.`personnel_id` 
			WHERE 	`attendance_register`.`date` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'" 
				AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_PERSONNEL.') 
				AND `personnel`.`enabled` = -1
				AND `personnel`.`left_date` IS NULL 
			ORDER BY `date` ASC;';

			$attendance = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
					$attendance[] = $obj;
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $attendance;
		}
		
		public function get_locations()
		{
			if(!self::user_has_permission( ATC_PERMISSION_LOCATIONS_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'SELECT * FROM `location` ORDER BY LOWER(`name`) ASC;';

			$activities = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$activities[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return $activities;
		}
		
		public function get_personnel( $id, $orderby = "ASC", $access_rights=null )
		{
			$personnel = new stdClass();

			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this user");
				
			switch( $id )
			{
				// only loose casting, so work it out properly here
				case null:
				case 0:
					if( is_null($id) )
					{
						$personnel = array();
						$query = "SELECT * FROM `personnel` ";
						if( !is_null($access_rights) )
							$query .= ' WHERE `access_rights` IN ('.self::$mysqli->real_escape_string($access_rights).')  AND `personnel_id` > 0 ';
						else 
							$query .= ' WHERE `personnel_id` > 0 ';
						$query .= "ORDER BY `enabled` ASC, `lastname` ".self::$mysqli->real_escape_string($orderby).", `firstname` ".self::$mysqli->real_escape_string($orderby).", `personnel_id` ".self::$mysqli->real_escape_string($orderby).";";

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
		private function log_action( $table_name, $sql_run, $idrow )
		{
			$query = "INSERT INTO `log_changes` (`personnel_id`, `sql_executed`, `table_updated`, `row_updated` ) VALUES ( ".self::$currentuser.', "'.self::$mysqli->real_escape_string($sql_run).'", "'.self::$mysqli->real_escape_string($table_name).'", '.(int)$idrow.' );';
			if ($result = self::$mysqli->query($query))	return true;
			else throw new ATCExceptionDBError(self::$mysqli->error);
		}
		
		public function set_activity( $activity_id, $startdate, $enddate, $title, $location_id, $personnel_id, $activity_type_id, $dress_code, $attendees )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");

			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid startdate');
			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid enddate');
			if( $dress_code != ATC_DRESS_CODE_BLUES && $dress_code != ATC_DRESS_CODE_DPM && $dress_code != ATC_DRESS_CODE_BLUES_AND_DPM )
				throw new ATCExceptionBadData('Unknown dress code value');

			$officers = self::get_personnel(null,'ASC',ATC_USER_GROUP_OFFICERS);
			$isofficer = false;
			foreach( $officers as $officer )
			{
				if( $officer->personnel_id == $personnel_id )
				{
					$isofficer = true;
					break;
				}
			}
			if( !$isofficer )
				throw new ATCExceptionBadData('Personnel needs to be an officer');

			if( !(int)$activity_id )
			{
				$query = "
					INSERT INTO `activity` (
						`startdate`,
						`enddate`,
						`personnel_id`,
						`title`,
						`location_id`,
						`activity_type_id`,
						`dress_code`
					) VALUES ( 
						'".date("Y-m-d H:i",strtotime($startdate))."',
						'".date("Y-m-d H:i",strtotime($enddate))."',
						".(int)$personnel_id.",
						'".self::$mysqli->real_escape_string($title)."', 
						".(int)$location_id.",
						".(int)$activity_type_id.",
						".(int)$dress_code."
					);";
				if ($result = self::$mysqli->query($query))
				{
					$activity_id = self::$mysqli->insert_id;
					self::log_action( 'activity', $query, $activity_id );
					$attendees = explode(',', $attendees);
					foreach($attendees as $personnel_id)
						if( $personnel_id )
							self::$mysqli->query("INSERT INTO `activity_register` (`activity_id`, `personnel_id`) VALUES (".(int)$activity_id.", ".(int)$personnel_id.");");
					return $activity_id;
				}
				else throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = "
					UPDATE `activity` SET 
						`startdate` = '".date("Y-m-d H:i",strtotime($startdate))."',
						`enddate` = '".date("Y-m-d H:i",strtotime($enddate))."',
						`personnel_id` = ".(int)$personnel_id.",
						`title` = '".self::$mysqli->real_escape_string($title)."', 
						`location_id` = ".(int)$location_id.",
						`activity_type_id` = ".(int)$activity_type_id.",
						`dress_code` = ".(int)$dress_code."
					WHERE `activity_id` = ".(int)$activity_id."
					LIMIT 1;";
				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'activity', $query, (int)$activity_id );
					$attendees = explode(",", $attendees);
					self::$mysqli->query("DELETE FROM `activity_register` WHERE `activity_id` = ".(int)$activity_id.";");
					foreach($attendees as $personnel_id)
						if( $personnel_id )
							self::$mysqli->query("INSERT INTO `activity_register` (`activity_id`, `personnel_id`) VALUES (".(int)$activity_id.", ".(int)$personnel_id.");");
					return (int)$activity_id;
				}
				else throw new ATCExceptionDBError(self::$mysqli->error);
			
			}
		}

		public function set_activity_type( $activity_type_id, $type, $status=null )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITY_TYPE_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this activity_type");
			if( !strlen(trim($type)) )
				throw new ATCExceptionBadData('Invalid activity type');

			if( !$activity_type_id )
			{
				$query = 'INSERT INTO `activity_type` (`type`, `nzcf_status` ) VALUES ( "'.self::$mysqli->real_escape_string($type).'", '.(is_null($status)?ATC_ACTIVITY_RECOGNISED:(int)$status).' );';
				if ($result = self::$mysqli->query($query))
				{
					$activity_type_id = self::$mysqli->insert_id;
					self::log_action( 'activity_type', $query, $activity_type_id );
					return $activity_type_id;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = 'UPDATE `activity_type` SET `type` = "'.self::$mysqli->real_escape_string($type).'"'.(is_null($status)?'':',`nzcf_status` = '.(int)$status).' WHERE activity_type_id = '.(int)$activity_type_id.' LIMIT 1;';

				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'activity_type', $query, $activity_type_id );
					return $activity_type_id;
				} else
					throw new ATCExceptionDBError(self::$mysqli->error);
				
			}
			return false;
		}
		
		public function set_attendance_register( $personnel_id, $date, $presence )
		{
			if( !(int)$personnel_id ) 
				throw new ATCExceptionBadData('Invalid personnel ID');
			if( !strtotime($date) )
				throw new ATCExceptionBadData('Invalid date');
			if( trim($presence) == "" )
			{
				$query = "DELETE FROM `attendance_register` WHERE `personnel_id` = ".(int)$personnel_id." AND `date` = '".date("Y-m-d",strtotime($date))."';";
				if ($result = self::$mysqli->query($query))
					self::log_action( 'attendance_register', $query, $personnel_id );
				else
					throw new ATCExceptionDBError(self::$mysqli->error);
				return true;
			}
			if( $presence != ATC_ATTENDANCE_PRESENT && $presence != ATC_ATTENDANCE_ON_LEAVE && $presence != ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE )
				throw new ATCExceptionBadData('Unknown presence value');

			$query = "INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`) VALUES ( ".(int)$personnel_id.", '".date("Y-m-d",strtotime($date))."', ".$presence.") ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)";
			if ($result = self::$mysqli->query($query))
				self::log_action( 'attendance_register', $query, $personnel_id );
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return true;
		}
		
		public function set_location( $location_id, $name, $address )
		{
			if(!self::user_has_permission( ATC_PERMISSION_LOCATIONS_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this location");
			if( !strlen(trim($name)) )
				throw new ATCExceptionBadData('Invalid name');

			if( !$location_id )
			{
				$query = 'INSERT INTO `location` (`name`, `address` ) VALUES ( "'.self::$mysqli->real_escape_string($name).'", "'.self::$mysqli->real_escape_string($address).'" );';
				if ($result = self::$mysqli->query($query))
				{
					$location_id = self::$mysqli->insert_id;
					self::log_action( 'location', $query, $location_id );
					return $location_id;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = 'UPDATE `location` SET `name` = "'.self::$mysqli->real_escape_string($name).'", `address` = "'.self::$mysqli->real_escape_string($address).'" WHERE location_id = '.(int)$location_id.' LIMIT 1;';

				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'location', $query, $location_id );
					return $location_id;
				} else
					throw new ATCExceptionDBError(self::$mysqli->error);
				
			}
			return false;
		}
		
		public function set_personnel( &$user )
		{
			$query = "";
			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $user->personnel_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this user");
				
			if( !$user->personnel_id )
			{
				$query = "INSERT INTO `personnel` (`firstname`, `lastname`, `email`, `dob`, `password`, `joined_date`, `left_date`, `access_rights`, `is_female`, `enabled` ) VALUES ( ";
				$query .= '"'.self::$mysqli->real_escape_string($user->firstname).'", "'.self::$mysqli->real_escape_string($user->lastname).'", "'.self::$mysqli->real_escape_string($user->email).'", "'.date('Y-m-d',strtotime($user->dob)).'", ';
				$query .= '"'.self::$mysqli->real_escape_string(create_hash($user->password)).'", "'.date('Y-m-d',strtotime($user->joined_date)).'", '.(strtotime($user->left_date)?'"'.date('Y-m-d',strtotime($user->left_date)).'"':'NULL').', '.(int)$user->access_rights.', ';
				$query .= (int)$user->is_female.', '.(isset($user->enabled)&&$user->enabled==-1?-1:0).' );';
				if ($result = self::$mysqli->query($query))
				{
					$user->personnel_id = self::$mysqli->insert_id;
					self::log_action( 'personnel', $query, $user->personnel_id );
					return true;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = 'UPDATE `personnel` SET `firstname` = "'.self::$mysqli->real_escape_string($user->firstname).'", `lastname` = "'.self::$mysqli->real_escape_string($user->lastname).'", `email` = "'.self::$mysqli->real_escape_string($user->email).'", `dob` = "'.date('Y-m-d',strtotime($user->dob)).'", ';
				if( strlen(trim($user->password)) ) 
					 $query .= '`password` = "'.self::$mysqli->real_escape_string(create_hash($user->password)).'", ';
				$query .= '`joined_date` = "'.date('Y-m-d',strtotime($user->joined_date)).'", ';
				if( strtotime($user->left_date) )
					$query .= '`left_date` = "'.date('Y-m-d',strtotime($user->left_date)).'", ';
				else 
					$query .= '`left_date` = NULL, ';
				$query .= '`access_rights` = '.(int)$user->access_rights.', `enabled` = '.(isset($user->enabled)&&$user->enabled==-1?-1:0).', `is_female` = '.(int)$user->is_female;
				$query .= ' WHERE personnel_id = '.(int)$user->personnel_id.' LIMIT 1;';

				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'personnel', $query, $user->personnel_id );
					return true;
				} else
					throw new ATCExceptionDBError(self::$mysqli->error);
				
			}
			return false;
		}	
		
		public function gui_output_page_footer( $title )
		{
			echo '
		<footer>
			<p> Built on the ATC system code available at <a target="blank" href="https://github.com/PhilTanner/ATC_system">https://github.com/PhilTanner/ATC_system</a> </p>
			<!-- <img src="49squadron.png" style="position:absolute; bottom: 1em; right: 1em; z-index: -1;" /> -->
		</footer>
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
		<script type="text/javascript" src="touchpunch.furf.com_jqueryui-touch.js"></script>
		<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
		
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		
		<script type="text/javascript">
			$(function(){
				$(".navoptions ul li a").button().addClass("ui-state-disabled");
				$(".navoptions ul li a.home").button({ icons: { primary: "ui-icon-home" } }).removeClass("ui-state-disabled")'.($title=='Home'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.personnel").button({ icons: { primary: "ui-icon-person" } }).removeClass("ui-state-disabled")'.($title=='Personnel'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.attendance").button({ icons: { primary: "ui-icon-clipboard" } }).removeClass("ui-state-disabled")'.($title=='Attendance'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.activities").button({ icons: { primary: "ui-icon-image" } }).removeClass("ui-state-disabled")'.($title=='Activities'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.finance").button({ icons: { primary: "ui-icon-cart" } });
				$(".navoptions ul li a.stores").button({ icons: { primary: "ui-icon-tag" } });
				$(".navoptions ul li a.training").button({ icons: { primary: "ui-icon-calendar" } });
			});
			
		</script>
		
	</head>
	<body>
		<div id="dialog"></div>
		<nav class="navoptions">
			<ul>
				<li> <a href="./" class="home">Home</a> </li>
				<li> <a href="./personnel.php" class="personnel">Personnel</a> </li>
				<li> <a href="./attendance.php" class="attendance">Attendance</a> </li>
				<li> <a href="./activities.php" class="activities">Activities</a> </li>
				<li> <a href="./" class="finance">Finance</a> </li>
				<li> <a href="./" class="stores">Stores</a> </li>
				<li> <a href="./" class="training">Training</a> </li>
			</ul>
		</nav>
		<h1> <!-- ATC --> '.$title.' </h1>
';
		}
		
		public function user_has_permission( $permission, $target=null )
		{
			if(ATC_DEBUG) return true;
		}
		
	}
?>

