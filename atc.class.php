<?php
	if( $_SERVER['HTTP_HOST'] == '49sqn.philtanner.com')
		define( 'ATC_DEBUG', 					0 );
	else
		define( 'ATC_DEBUG', 					1 );
	
	// Permissions structure, as a bitmask
	define( 'ATC_PERMISSION_PERSONNEL_VIEW', 		1 );
	define( 'ATC_PERMISSION_PERSONNEL_EDIT',		1 << 1 );
	define( 'ATC_PERMISSION_ATTENDANCE_VIEW',		1 << 2 );
	define( 'ATC_PERMISSION_ATTENDANCE_EDIT',		1 << 3 );
	define( 'ATC_PERMISSION_ACTIVITIES_VIEW',		1 << 4 );
	define( 'ATC_PERMISSION_ACTIVITIES_EDIT',		1 << 5 );
	define( 'ATC_PERMISSION_FINANCE_VIEW',			1 << 6 );
	define( 'ATC_PERMISSION_FINANCE_EDIT',			1 << 7 );
	define( 'ATC_PERMISSION_SYSTEM_VIEW',			1 << 8 );
	define( 'ATC_PERMISSION_SYSTEM_EDIT',			1 << 9 );
	define( 'ATC_PERMISSION_STORES_VIEW',			1 << 10 );
	define( 'ATC_PERMISSION_STORES_EDIT',			1 << 11 );
	define( 'ATC_PERMISSION_LOCATIONS_VIEW',		1 << 12 );
	define( 'ATC_PERMISSION_LOCATIONS_EDIT',		1 << 13 );
	define( 'ATC_PERMISSION_ACTIVITY_TYPE_EDIT',		1 << 14 );
	define( 'ATC_PERMISSION_TRAINING_VIEW',		1 << 15 );
	define( 'ATC_PERMISSION_TRAINING_EDIT',		1 << 16 );

	// Give admin everything we can think of in the future (max value of MySQL mediumint unsigned field access_rights).
	define( 'ATC_USER_LEVEL_ADMIN',			(1 << 24) - 1);
	
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
	define( 'ATC_DRESS_CODE_MUFTI',			3 );
	
	define( 'ATC_NOK_TYPE_MOTHER',				0 );
	define( 'ATC_NOK_TYPE_FATHER',				1 );
	define( 'ATC_NOK_TYPE_STEPMOTHER',			2 );
	define( 'ATC_NOK_TYPE_STEPFATHER',			3 );
	define( 'ATC_NOK_TYPE_SPOUSE',				4 );
	define( 'ATC_NOK_TYPE_SIBLING',				5 );
	define( 'ATC_NOK_TYPE_DOMPTNR',				6 );
	define( 'ATC_NOK_TYPE_OTHER',				7 );
	define( 'ATC_NOK_TYPE_GRANDMOTHER',			8 );
	define( 'ATC_NOK_TYPE_GRANDFATHER',			9 );

	require_once 'config.php';
	
	/* The user levels are set in the config file, so groups can't be declared until afterwards */
	define( 'ATC_USER_GROUP_OFFICERS',			ATC_USER_LEVEL_ADJUTANT.','.ATC_USER_LEVEL_STORES.','.ATC_USER_LEVEL_TRAINING.','.ATC_USER_LEVEL_CUCDR.','.ATC_USER_LEVEL_SUPOFF.','.ATC_USER_LEVEL_OFFICER );
	define( 'ATC_USER_GROUP_CADETS',			ATC_USER_LEVEL_CADET.','.ATC_USER_LEVEL_SNCO );
	define( 'ATC_USER_GROUP_PERSONNEL',			ATC_USER_GROUP_OFFICERS.','.ATC_USER_GROUP_CADETS );
	
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
	class ATCExceptionInvalidUserSession extends ATCExceptionInsufficientPermissions {}
	
	class ATC
	{
		protected static $mysqli;
		protected static $currentuser;
		protected static $currentpermissions;
		
		public function __construct()
		{
			self::$mysqli = new mysqli(ATC_SETTING_DB_HOST, ATC_SETTING_DB_USER, ATC_SETTING_DB_PSWD, ATC_SETTING_DB_NAME);
			/* check connection */
			if (mysqli_connect_errno())
			    throw new ATCExceptionDBConn(mysqli_connect_error());
			
			if( isset($_COOKIE['sessid']) )
			{
				try {
					$details = self::check_user_session($_COOKIE['sessid']);
					self::$currentuser = $details->personnel_id;
					self::$currentpermissions = $details->access_rights; 
					if(!self::$currentuser && substr($_SERVER['SCRIPT_NAME'], -9, 9) != "login.php" )
						header('Location: login.php', true, 302);
				} catch (ATCExceptionInvalidUserSession $e) {
					if(substr($_SERVER['SCRIPT_NAME'], -9, 9) != "login.php" )
						header('Location: login.php', true, 302);
				}
			} else 
				if(substr($_SERVER['SCRIPT_NAME'], -9, 9) != "login.php" )
					header('Location: login.php', true, 302);
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
		
		public function add_promotion( $rank_id, $personnel_id, $date )
		{
			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $personnel_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = "INSERT INTO `personnel_rank` (`rank_id`, `personnel_id`, `date_achieved` ) VALUES ( ".(int)$rank_id.", ".(int)$personnel_id.", '".date("Y-m-d",strtotime($date))."' );";

			if ($result = self::$mysqli->query($query))
			{
				self::log_action( 'personnel_rank', $query, self::$mysqli->insert_id );
				return self::$mysqli->insert_id;
			}
			else throw new ATCExceptionDBError(self::$mysqli->error);
		}

		public function check_user_session( $session )
		{
			$query = "SELECT * FROM `user_session` INNER JOIN `personnel` ON `user_session`.`personnel_id` = `personnel`.`personnel_id` WHERE `session_code` = '".self::$mysqli->real_escape_string($session)."' LIMIT 1;";
			if ($result = self::$mysqli->query($query))	
			{
				if ( $obj = $result->fetch_object() )
				{
					return $obj;
				} else throw new ATCExceptionInvalidUserSession('Unknown session');
			}
			else throw new ATCExceptionDBError(self::$mysqli->error);
			return false;
		}

/*
		public function __destruct()
		{
			//self::$mysqli->close();
		}
		*/
		
		public function current_user_id()
		{
			return self::$currentuser;
		}
		
		public function delete_activity( $id )
		{
			// Also don't allow deletes of default values
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_EDIT ) || !(int)$id )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'DELETE FROM `activity` WHERE `activity`.`activity_id` = '.(int)$id.' LIMIT 1;';

			if ($result = self::$mysqli->query($query))
			{
				self::log_action( 'activity', $query, (int)$id );
				return true;
			} else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return false;
		}
		
		public function get_activities( $date=null, $days=365 )
		{
			if( is_null($date) ) $startdate = strtotime(date("Y")."-01-01");
			else $startdate = strtotime($date);
			$enddate = $startdate + ((int)$days*24*60*60);
			
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = '
				SELECT	`activity`.*,
					`activity_type`.*,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					`personnel`.`personnel_id`,
					'.str_replace("personnel","2ic_personnel",ATC_SETTING_DISPLAY_NAME).' AS `twoic_display_name`,
					`2ic_personnel`.`personnel_id` AS `twoic_personnel_id`,
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
					) AS `cadets_attending`,
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
					INNER JOIN `personnel` `2ic_personnel`
						ON `activity`.`2ic_personnel_id` = `2ic_personnel`.`personnel_id`
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
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					`personnel`.`mobile_phone`,
					`personnel`.`personnel_id`,
					'.str_replace("personnel","2ic_personnel",ATC_SETTING_DISPLAY_NAME).' AS `twoic_display_name`,
					`2ic_personnel`.`mobile_phone` AS `twoic_mobile_phone`,
					`2ic_personnel`.`personnel_id` AS `twoic_personnel_id`,
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
					INNER JOIN `personnel` `2ic_personnel`
						ON `activity`.`2ic_personnel_id` = `2ic_personnel`.`personnel_id`
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
					( 
   					SELECT `rank_shortname` 
   					FROM `personnel_rank` 
							INNER JOIN `rank` 
								ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   					WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   					ORDER BY `date_achieved` DESC 
   					LIMIT 1 
					) AS `rank`,
					`personnel`.`mobile_phone`,
					`personnel`.`allergies`,
					`personnel`.`access_rights`,
					`personnel`.`medical_conditions`,
					`personnel`.`medicinal_reactions`,
					`personnel`.`dietary_requirements`,
					`personnel`.`other_notes`,
					`personnel`.`social_media_approved`
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
			ORDER BY `personnel`.`personnel_id`, `attendance_register`.`date` ASC;';

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
		
		public function get_awol( $startdate, $enddate )
		{
			$startdate = strtotime($startdate);
			$enddate = strtotime($enddate);

			if(!self::user_has_permission( ATC_PERMISSION_ATTENDANCE_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
			SELECT	`attendance_register`.*,
				`personnel`.*,
				'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`
			FROM 	`attendance_register` 
				INNER JOIN `personnel` 
					ON `attendance_register`.`personnel_id` = `personnel`.`personnel_id` 
			WHERE 	`attendance_register`.`date` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'"  
				AND `attendance_register`.`presence` = '.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE.'
				-- AND ( LENGTH(`attendance_register`.`comment`) = 0 OR LENGTH(`attendance_register`.`comment`) IS NULL )
				AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_PERSONNEL.') 
			ORDER BY `date` DESC, `display_name` ASC;';

			$awollers = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
				{
					$obj->nok = self::get_nok($obj->personnel_id);
					$awollers[] = $obj;
				}
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return $awollers;
		}
		
		public function get_currentuser_id() { return self::$currentuser; }
		
		public function get_flights()
		{
				
			$query = 'SELECT DISTINCT `flight` FROM `personnel` WHERE LENGTH(TRIM(`flight`)) > 0 ORDER BY LOWER(`flight`);';

			$flights = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$flights[] = $obj->flight;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			return $flights;
		}
		
		public function get_location( $id=0 )
		{
			if(!self::user_has_permission( ATC_PERMISSION_LOCATIONS_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = 'SELECT * FROM `location` WHERE `location_id` = '.(int)$id.' LIMIT 1;';

			if ($result = self::$mysqli->query($query))
				return $result->fetch_object();
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
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
		
		public function get_nok( $for_personnel_id, $nok_id=null )
		{
			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $for_personnel_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this user");
				
			$query = '
			SELECT	*
			FROM 	`next_of_kin`
			WHERE 	`personnel_id` = '.(int)$for_personnel_id.'
			ORDER BY `sort_order`, `lastname`,`firstname` ASC;';

			$nok = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
					$nok[] = $obj;
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $nok;
		}
		
		
		public function get_personnel( $id, $orderby = "ASC", $access_rights=null, $showall=false )
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
						$query = "
							SELECT 	*, 
								".ATC_SETTING_DISPLAY_NAME." AS `display_name`,
								( 
   								SELECT `rank_shortname` 
   								FROM `personnel_rank` 
										INNER JOIN `rank` 
											ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   								WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   								ORDER BY `date_achieved` DESC 
   								LIMIT 1 
								) AS `rank`
							FROM `personnel` 
							WHERE `personnel_id` > 0 ";
						if( !is_null($access_rights) )
							$query .= ' AND `access_rights` IN ('.self::$mysqli->real_escape_string($access_rights).') ';
						
						if( !(bool)$showall )
							$query .= " AND `enabled` = -1 AND `access_rights` IN (".ATC_USER_GROUP_PERSONNEL.") AND `left_date` IS NULL";
						$query .= " ORDER BY `enabled` ASC, `lastname` ".self::$mysqli->real_escape_string($orderby).", `firstname` ".self::$mysqli->real_escape_string($orderby).", `personnel_id` ".self::$mysqli->real_escape_string($orderby).";";

						if ($result = self::$mysqli->query($query))
						{
							while ( $obj = $result->fetch_object() )
								$personnel[] = $obj;
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
					if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $id ))
					    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this user");
				
					$query = "SELECT *,
						".ATC_SETTING_DISPLAY_NAME." AS `display_name`,
					( 
   					SELECT `rank_shortname` 
   					FROM `personnel_rank` 
							INNER JOIN `rank` 
								ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   					WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   					ORDER BY `date_achieved` DESC 
   					LIMIT 1 
					) AS `rank` 
					FROM `personnel` 
					WHERE `personnel_id` = ".(int)$id." 
					LIMIT 1;";
					
					if ($result = self::$mysqli->query($query)) 
						$personnel = $result->fetch_object();
					else
						throw new ATCExceptionDBError(self::$mysqli->error);
					$personnel->created = date("Y-m-d\TH:i", strtotime($personnel->created));
					
					break;
			}
			return $personnel;
		}
		
		public function get_promotion_history( $userid )
		{
			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $userid ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = '
				SELECT * 
				FROM `personnel_rank`
					INNER JOIN `rank`
						ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
				WHERE `personnel_id` = '.(int)$userid.' 
				ORDER BY `date_achieved` DESC;';

			$promotions = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
					$promotions[] = $obj;
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $promotions;
		}
		
		public function get_ranks( )
		{
			$query = '
				SELECT * 
				FROM `rank`
				ORDER BY `ordering` ASC;';

			$ranks = array();
			if ($result = self::$mysqli->query($query))
			{
				while ( $obj = $result->fetch_object() )
					$ranks[] = $obj;
			}	
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $ranks;
		}
		
		// Keep a track of who's doing what, for later auditing.
		private function log_action( $table_name, $sql_run, $idrow )
		{
			$query = "INSERT INTO `log_changes` (`personnel_id`, `sql_executed`, `table_updated`, `row_updated` ) VALUES ( ".self::$currentuser.', "'.self::$mysqli->real_escape_string($sql_run).'", "'.self::$mysqli->real_escape_string($table_name).'", '.(int)$idrow.' );';
			if ($result = self::$mysqli->query($query))	return true;
			else throw new ATCExceptionDBError(self::$mysqli->error);
		}
		
		public function login( $username, $password )
		{
			$query = "SELECT `password` AS `correct_hash`, `personnel_id` FROM `personnel` WHERE `email` = '".self::$mysqli->real_escape_string($username)."' AND `enabled` = -1 LIMIT 1;";
			if ($result = self::$mysqli->query($query))	
			{
				if ( $obj = $result->fetch_object() )
				{
					if( validate_password($password, $obj->correct_hash) )
					{
						// TODO - catch unlikely key conflict to existing user
						$uniqueid = bin2hex(openssl_random_pseudo_bytes(32));
						$query = "INSERT INTO `user_session` (`personnel_id`, `session_code`, `user_agent`, `ip_address` ) VALUES ( ".$obj->personnel_id.", '".self::$mysqli->real_escape_string($uniqueid)."', '".self::$mysqli->real_escape_string($_SERVER['HTTP_USER_AGENT'])."', ".ip2long($_SERVER['REMOTE_ADDR'])." );";
						if ($result = self::$mysqli->query($query))
						{
							setcookie( 'sessid', $uniqueid, time()+60*60*24*30 );
							return true;
						} else throw new ATCExceptionDBError(self::$mysqli->error);
					} else throw new ATCExceptionInsufficientPermissions('Unknown username or password');
				} else throw new ATCExceptionInsufficientPermissions('Unknown username or password');
			}
			else throw new ATCExceptionDBError(self::$mysqli->error);
			return false;
		}
		
		public function logout( $sessid=null )
		{
			if( is_null($sessid) )
				$sessid = $_COOKIE['sessid'];
				
			$query = "DELETE FROM `user_session` WHERE `session_code` =  '".self::$mysqli->real_escape_string($sessid)."'";
			// Only allow non user editors to log out their own user. People with this permission can log out anyone else
			if(self::user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT ))
				$query .= " AND `personnel_id` = ".(int)self::$currentuser;
			$query .= " LIMIT 1;";

			if ($result = self::$mysqli->query($query))	
				self::log_action( 'user_session', $query, 0 );
			else throw new ATCExceptionDBError(self::$mysqli->error);
			return true;
		}
		
		
		public function set_activity( $activity_id, $startdate, $enddate, $title, $location_id, $personnel_id, $twoic_personnel_id, $activity_type_id, $dress_code, $attendees, $cost )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_EDIT, $activity_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");

			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid startdate');
			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid enddate');
			if( $dress_code != ATC_DRESS_CODE_BLUES && $dress_code != ATC_DRESS_CODE_DPM && $dress_code != ATC_DRESS_CODE_BLUES_AND_DPM && $dress_code != ATC_DRESS_CODE_MUFTI )
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
				throw new ATCExceptionBadData('OIC needs to be an officer');
			$isofficer = false;
			foreach( $officers as $officer )
			{
				if( $officer->personnel_id == $twoic_personnel_id )
				{
					$isofficer = true;
					break;
				}
			}
			if( !$isofficer )
				throw new ATCExceptionBadData('Alternate OIC needs to be an officer');
			
			if( !(int)$activity_id )
			{
				$query = "
					INSERT INTO `activity` (
						`startdate`,
						`enddate`,
						`personnel_id`,
						`2ic_personnel_id`,
						`title`,
						`location_id`,
						`activity_type_id`,
						`dress_code`,
						`cost`
					) VALUES ( 
						'".date("Y-m-d H:i",strtotime($startdate))."',
						'".date("Y-m-d H:i",strtotime($enddate))."',
						".(int)$personnel_id.",
						".(int)$twoic_personnel_id.",
						'".self::$mysqli->real_escape_string($title)."', 
						".(int)$location_id.",
						".(int)$activity_type_id.",
						".(int)$dress_code.",
						".(float)$cost."
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
						`2ic_personnel_id` = ".(int)$twoic_personnel_id.",
						`title` = '".self::$mysqli->real_escape_string($title)."', 
						`location_id` = ".(int)$location_id.",
						`activity_type_id` = ".(int)$activity_type_id.",
						`dress_code` = ".(int)$dress_code.",
						`cost` = ".(float)$cost."
					WHERE `activity_id` = ".(int)$activity_id."
					LIMIT 1;";
				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'activity', $query, (int)$activity_id );
					$attendees = explode(",", $attendees);
					// Remove everyone, simpler than working out who has been dragged out of the box
					// But only remove them if they're not recorded as attending already. At that point, not sure what we do, but DB leaves them alone
					self::$mysqli->query("DELETE FROM `activity_register` WHERE `activity_id` = ".(int)$activity_id." AND `presence` IS NULL;");
					foreach($attendees as $personnel_id)
						if( $personnel_id )
							self::$mysqli->query("INSERT INTO `activity_register` (`activity_id`, `personnel_id`) VALUES (".(int)$activity_id.", ".(int)$personnel_id.");");
					return (int)$activity_id;
				}
				else throw new ATCExceptionDBError(self::$mysqli->error);
			
			}
		}

		public function set_activity_attendance( $activity_id, $register )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");

			if( !is_array($register) )
				throw new ATCExceptionBadData('Invalid registration details');

			if( !(int)$activity_id )
				throw new ATCExceptionBadData('Invalid activity identifier');
			
			foreach( $register as $key => $value )
			{
				$personnel_id = (int)$value['personnel_id'];
				$presence = $value['attendance'];
				if( $presence == '' )
					$presence = 'NULL';
				$note = $value['note'];
				$updatenote = strlen(trim($note));
				$amount_paid = (float)$value['amount_paid'];
				
				if( $presence != ATC_ATTENDANCE_PRESENT && $presence != ATC_ATTENDANCE_ON_LEAVE && $presence != ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE )
					throw new ATCExceptionBadData('Unknown presence value');

				$query = "INSERT INTO `activity_register` (`personnel_id`, `activity_id`, `presence`".($updatenote?', `note`':'').", `amount_paid`) VALUES ( ".(int)$personnel_id.", ".(int)$activity_id.", ".$presence.($updatenote?", '".self::$mysqli->real_escape_string($note)."'":'').", ".$amount_paid.") ON DUPLICATE KEY UPDATE `presence` = ".$presence.($updatenote?", `note` = '".self::$mysqli->real_escape_string($note)."'":'').", `amount_paid` = ".$amount_paid.";";
				
				if ($result = self::$mysqli->query($query))
					self::log_action( 'activity_register', $query, $activity_id );
				else throw new ATCExceptionDBError(self::$mysqli->error);
			}
			return $activity_id;
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
		
		public function set_attendance_register( $personnel_id, $date, $presence, $comment=null )
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

			$query = "INSERT INTO `attendance_register` (`personnel_id`, `date`, `presence`".(is_null($comment)?'':', `comment`').") VALUES ( ".(int)$personnel_id.", '".date("Y-m-d",strtotime($date))."', ".$presence.(is_null($comment)?'':', "'.self::$mysqli->real_escape_string($comment).'"').") ON DUPLICATE KEY UPDATE `presence` = VALUES(`presence`)".(is_null($comment)?'':', `comment` = VALUES(`comment`)');
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
		
		public function set_next_of_kin( $nokid, $personnel_id, $firstname, $lastname, $relationship, $email, $mobile, $home, $address1, $address2, $city, $postcode, $sortorder=0 )
		{
			if(!self::user_has_permission( ATC_PERMISSION_PERSONNEL_EDIT, $personnel_id ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this user");
			
			if( !strlen(trim($firstname)) )
				throw new ATCExceptionBadData('Invalid first name');
			if( !strlen(trim($lastname)) )
				throw new ATCExceptionBadData('Invalid last name');
			if( !strlen(trim($email)) )
				throw new ATCExceptionBadData('Invalid email');
			if( !strlen(trim($mobile)) )
				throw new ATCExceptionBadData('Invalid mobile');
			if( !strlen(trim($address1)) )
				throw new ATCExceptionBadData('Invalid address line 1');
			if( !strlen(trim($city)) )
				throw new ATCExceptionBadData('Invalid city');

			if( !$nokid )
			{
				$query = '
					INSERT INTO `next_of_kin` (
						`personnel_id`, 
						`firstname`, 
						`lastname`, 
						`email`, 
						`relationship`, 
						`mobile_number`, 
						`home_number`, 
						`address1`, 
						`address2`, 
						`city`, 
						`postcode`, 
						`sort_order`
					) VALUES (
						'.(int)$personnel_id.',
						"'.self::$mysqli->real_escape_string($firstname).'", 
						"'.self::$mysqli->real_escape_string($lastname).'", 
						"'.self::$mysqli->real_escape_string($email).'", 
						'.(int)$relationship.', 
						"'.self::$mysqli->real_escape_string($mobile).'", 
						"'.self::$mysqli->real_escape_string($home).'", 
						"'.self::$mysqli->real_escape_string($address1).'", 
						"'.self::$mysqli->real_escape_string($address2).'", 
						"'.self::$mysqli->real_escape_string($city).'", 
						'.(int)$postcode.', 
						'.(int)$sortorder.'
					);';
				if ($result = self::$mysqli->query($query))
				{
					$nok_id = self::$mysqli->insert_id;
					self::log_action( 'location', $query, $nok_id );
					return $nok_id;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = '
					UPDATE `next_of_kin` SET 
						`personnel_id` = '.(int)$personnel_id.',
						`firstname` = "'.self::$mysqli->real_escape_string($firstname).'", 
						`lastname` = "'.self::$mysqli->real_escape_string($lastname).'", 
						`email` = "'.self::$mysqli->real_escape_string($email).'", 
						`relationship` = '.(int)$relationship.', 
						`mobile_number` = "'.self::$mysqli->real_escape_string($mobile).'", 
						`home_number` = "'.self::$mysqli->real_escape_string($home).'", 
						`address1` = "'.self::$mysqli->real_escape_string($address1).'", 
						`address2` = "'.self::$mysqli->real_escape_string($address2).'", 
						`city` = "'.self::$mysqli->real_escape_string($city).'", 
						`postcode` = '.(int)$postcode.',
						`sort_order` = '.(int)$sortorder.'
					WHERE `kin_id` = '.(int)$nokid.'
					LIMIT 1;';
				if ($result = self::$mysqli->query($query))
				{
					self::log_action( 'location', $query, (int)$nokid );
					return $nokid;
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
				$query = "INSERT INTO `personnel` (`firstname`, `lastname`, `email`, `mobile_phone`, `allergies`, `medical_conditions`, `medicinal_reactions`, `dietary_requirements`, `other_notes`, `dob`, `password`, `joined_date`, `left_date`, `access_rights`, `is_female`, `enabled`, `flight`, `social_media_approved` ) VALUES ( ";
				$query .= '"'.self::$mysqli->real_escape_string($user->firstname).'", "'.self::$mysqli->real_escape_string($user->lastname).'", "'.self::$mysqli->real_escape_string($user->email).'", "'.self::$mysqli->real_escape_string($user->mobile_phone).'", "'.self::$mysqli->real_escape_string($user->allergies).'", "'.self::$mysqli->real_escape_string($user->medical_conditions).'", "'.self::$mysqli->real_escape_string($user->medicinal_reactions).'", "'.self::$mysqli->real_escape_string($user->dietary_requirements).'", "'.self::$mysqli->real_escape_string($user->other_notes).'", "'.date('Y-m-d',strtotime($user->dob)).'", ';
				$query .= '"'.self::$mysqli->real_escape_string(create_hash($user->password)).'", "'.date('Y-m-d',strtotime($user->joined_date)).'", '.(strtotime($user->left_date)?'"'.date('Y-m-d',strtotime($user->left_date)).'"':'NULL').', '.(int)$user->access_rights.', ';
				$query .= (int)$user->is_female.', '.(isset($user->enabled)&&$user->enabled==-1?-1:0).', "'.self::$mysqli->real_escape_string($user->flight).'", '.(isset($user->social_media_approved)&&$user->social_media_approved==-1?-1:0).' );';
				if ($result = self::$mysqli->query($query))
				{
					$user->personnel_id = self::$mysqli->insert_id;
					self::log_action( 'personnel', $query, $user->personnel_id );
					return true;
				} else 
					throw new ATCExceptionDBError(self::$mysqli->error);
			} else {
				$query = 'UPDATE `personnel` SET `firstname` = "'.self::$mysqli->real_escape_string($user->firstname).'", `lastname` = "'.self::$mysqli->real_escape_string($user->lastname).'", `email` = "'.self::$mysqli->real_escape_string($user->email).'", `mobile_phone` = "'.self::$mysqli->real_escape_string($user->mobile_phone).'", `allergies` = "'.self::$mysqli->real_escape_string($user->allergies).'", `medical_conditions` = "'.self::$mysqli->real_escape_string($user->medical_conditions).'", `medicinal_reactions` = "'.self::$mysqli->real_escape_string($user->medicinal_reactions).'", `dietary_requirements` = "'.self::$mysqli->real_escape_string($user->dietary_requirements).'",  `other_notes` = "'.self::$mysqli->real_escape_string($user->other_notes).'", `dob` = "'.date('Y-m-d',strtotime($user->dob)).'", ';
				if( strlen(trim($user->password)) ) 
					 $query .= '`password` = "'.self::$mysqli->real_escape_string(create_hash($user->password)).'", ';
				$query .= '`joined_date` = "'.date('Y-m-d',strtotime($user->joined_date)).'", ';
				if( strtotime($user->left_date) )
					$query .= '`left_date` = "'.date('Y-m-d',strtotime($user->left_date)).'", ';
				else 
					$query .= '`left_date` = NULL, ';
				$query .= '`access_rights` = '.(int)$user->access_rights.', `enabled` = '.(isset($user->enabled)&&$user->enabled==-1?-1:0).', `is_female` = '.(int)$user->is_female.', `flight` = "'.self::$mysqli->real_escape_string($user->flight).'", `social_media_approved` = '.(isset($user->social_media_approved)&&$user->social_media_approved==-1?-1:0).'';
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
		<script>
			$("thead th").button().removeClass("ui-corner-all").css({ display: "table-cell" });
			$("tbody tr:odd").not(".ui-state-highlight, .ui-state-error").addClass("evenrow");
			$("table.tablesorter").tablesorter().on("sortStart", function(){ $("tbody tr").removeClass("evenrow"); }).on("sortEnd", function(){ $("tbody tr:odd").not(".ui-state-highlight, .ui-state-error").addClass("evenrow"); });
		</script>
		<footer>
			<p> Built on the ATC system code available at <a target="blank" href="https://github.com/PhilTanner/ATC_system">https://github.com/PhilTanner/ATC_system</a> </p>
			'.(ATC_DEBUG?'<p style="font-size:75%;">DEBUG INFO: Logged in as user: '.self::$currentuser.' - access rights: '.self::$currentpermissions.'</p>':'').'
			'.(ATC_DEBUG?'<!--':'').'<img src="49squadron.png" style="position:absolute; bottom: 1em; right: 1em; z-index: -1;" />'.(ATC_DEBUG?'-->':'').'
		</footer>
		'.(ATC_DEBUG?'<style>body { color:red; }</style>':'').'
	</body>
</html>';
		}
		
		public function gui_output_page_header( $title )
		{
			echo '<!doctype html>
<html lang="us">
	<head>
		<meta charset="utf-8">
		<title>'.(ATC_DEBUG?'DEV':'ATC').' '.$title.'</title>
		<link href="jquery-ui-1.9.2.custom/css/redmond/jquery-ui-1.9.2.custom.css" rel="stylesheet">
		<link href="atc.css" rel="stylesheet">
		
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-1.8.3.js"></script>
		<script type="text/javascript" src="jquery-ui-1.9.2.custom/js/jquery-ui-1.9.2.custom.js"></script>
		<script type="text/javascript" src="touchpunch.furf.com_jqueryui-touch.js"></script>
		<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="tablesorter/jquery.tablesorter.js"></script> 
		
		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		
		<script type="text/javascript">
			$.tablesorter.addParser({
				// set a unique id
				id: "ATC_SETTING_DATETIME_OUTPUT",
				is: function(s) {
					// return false so this parser is not auto detected
					return  /^\d{1,2}[\ ][A-Za-z]{3}[\,][\ ]\d{2}[\:]\d{2}$/.test(s);;
				},
				format: function(s) {
					// format your data for normalization
					return Date.parse(s);
				},
				// set type, either numeric or text
				type: "numeric"
			});
			$.tablesorter.addParser({
				// set a unique id
				id: "ATC_SETTING_DATE_OUTPUT",
				is: function(s) {
					// return false so this parser is not auto detected
					return  /^\d{1,2}[\ ][A-Za-z]{3}$/.test(s);;
				},
				format: function(s) {
					// format your data for normalization
					return Date.parse(s);
				},
				// set type, either numeric or text
				type: "numeric"
			});
	
		
			$(function(){
				//$(".navoptions ul li a").button().addClass("ui-state-disabled");
				$(".navoptions ul li a.home").button({ icons: { primary: "ui-icon-home" } })'.(self::$currentuser?'.removeClass("ui-state-disabled")':'').($title=='Home'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.personnel").button({ icons: { primary: "ui-icon-contact" } })'.(self::$currentuser?'.removeClass("ui-state-disabled")':'').($title=='Personnel'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.attendance").button({ icons: { primary: "ui-icon-clipboard" } })'.(self::$currentuser?'.removeClass("ui-state-disabled")':'').($title=='Attendance'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.activities").button({ icons: { primary: "ui-icon-image" } })'.(self::$currentuser?'.removeClass("ui-state-disabled")':'').($title=='Activities'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.documents").button({ icons: { primary: "ui-icon-folder-open" } })'.(self::$currentuser?'.removeClass("ui-state-disabled")':'').($title=='Documentation'?'.addClass("ui-state-active")':'').';
				$(".navoptions ul li a.system").button({ icons: { primary: "ui-icon-gear" } }).removeClass("ui-state-disabled")'.($title=='System'?'.addClass("ui-state-active")':'').';
				
				$(".navoptions ul li a.finance").button({ icons: { primary: "ui-icon-cart" } });
				$(".navoptions ul li a.stores").button({ icons: { primary: "ui-icon-tag" } });
				$(".navoptions ul li a.training").button({ icons: { primary: "ui-icon-calendar" } });
				
				
				$(".navoptions ul li a.logout").button({ icons: { primary: "ui-icon-unlocked" } }).removeClass("ui-state-disabled");
				$(".navoptions ul li a.login").button({ icons: { primary: "ui-icon-locked" } })'.(self::$currentuser?'':'.removeClass("ui-state-disabled")').($title=='Login'?'.addClass("ui-state-active")':'').';
			});
			
		</script>
		
	</head>
	<body>
		<div id="dialog"></div>
		<nav class="navoptions">
			<ul>
				<li> <a href="./" class="home">Home</a> </li>
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW, self::$currentuser)?'<li> <a href="./personnel.php" class="personnel">Personnel</a> </li>':'').'
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_ATTENDANCE_VIEW)?'<li> <a href="./attendance.php" class="attendance">Attendance</a> </li>':'').'
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_ACTIVITIES_VIEW)?'<li> <a href="./activities.php" class="activities">Activities</a> </li>':'').'
				<!--'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW)?'<li> <a href="./" class="finance">Finance</a> </li>':'').'
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_STORES_VIEW)?'<li> <a href="./" class="stores">Stores</a> </li>':'').'
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_TRAINING_VIEW)?'<li> <a href="./" class="training">Training</a> </li>':'').'-->
				'.(self::$currentuser && self::user_has_permission(ATC_USER_LEVEL_ADJUTANT)?'<li> <a href="./documents.php" class="documents">Documentation</a> </li>':'').'
				'.(self::$currentuser && self::user_has_permission(ATC_PERMISSION_SYSTEM_VIEW)?'<li> <a href="./system.php" class="system">System</a> </li>':'').'
				
				'.(self::$currentuser?'<li> <a href="./logout.php" class="logout">Logout</a> </li>':'<li> <a href="./login.php" class="login">Login</a> </li>').'				
			</ul>
		</nav>
		<h1> '.(ATC_DEBUG?'<span style="color:Red;">DEV</span>':'ATC').' - '.$title.' </h1>
';
		}
		
		public function user_has_permission( $permission, $target=null )
		{
			if( is_null($target) )
			{				
				if( (self::$currentpermissions & $permission) == $permission ) 
					return true;
			} else {
								
				// If we have the global permission, we're good anyway
				if( (self::$currentpermissions & $permission) == $permission ) 
					return true;
				switch($permission)
				{
					case ATC_PERMISSION_PERSONNEL_VIEW:
					case ATC_PERMISSION_PERSONNEL_EDIT:
						// If we're wanting to view/edit our own user, we're all good.
						if( $target == self::$currentuser )
							return true;
						break;
					case ATC_PERMISSION_ACTIVITIES_EDIT:
						// If we're the OIC, we should be able to edit it.
						$query = 'SELECT `personnel_id` FROM `activity` WHERE `activity_id` = '.(int)$target.' LIMIT 1;';
						
						if ($result = self::$mysqli->query($query))
						{
							while ( $obj = $result->fetch_object() )
								// Make sure we're the OIC, and that we're logged in (otherwise anon users can edit new/misconfigured activities)
								if( $obj->personnel_id == self::$currentuser && self::$currentuser )
									return true;
						} else
							throw new ATCExceptionDBError(self::$mysqli->error);
						
						break;
					default:
						return 0;
				}
			}
			return 0;
		}
		
	}
	
	

	/*
	 * Password Hashing With PBKDF2 (http://crackstation.net/hashing-security.htm).
	 * Copyright (c) 2013, Taylor Hornby
	 * All rights reserved.
	 *
	 * Redistribution and use in source and binary forms, with or without 
	 * modification, are permitted provided that the following conditions are met:
	 *
	 * 1. Redistributions of source code must retain the above copyright notice, 
	 * this list of conditions and the following disclaimer.
	 *
	 * 2. Redistributions in binary form must reproduce the above copyright notice,
	 * this list of conditions and the following disclaimer in the documentation 
	 * and/or other materials provided with the distribution.
	 *
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
	 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
	 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE 
	 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
	 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
	 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
	 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
	 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
	 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	 * POSSIBILITY OF SUCH DAMAGE.
	 */
	
	// These constants may be changed without breaking existing hashes.
	define("PBKDF2_HASH_ALGORITHM", "sha256");
	define("PBKDF2_ITERATIONS", 1000);
	define("PBKDF2_SALT_BYTE_SIZE", 24);
	define("PBKDF2_HASH_BYTE_SIZE", 24);
	
	define("HASH_SECTIONS", 4);
	define("HASH_ALGORITHM_INDEX", 0);
	define("HASH_ITERATION_INDEX", 1);
	define("HASH_SALT_INDEX", 2);
	define("HASH_PBKDF2_INDEX", 3);
	
	function create_hash($password)
	{
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
		return PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" .  $salt . ":" .
			base64_encode(pbkdf2(
				PBKDF2_HASH_ALGORITHM,
				$password,
				$salt,
				PBKDF2_ITERATIONS,
				PBKDF2_HASH_BYTE_SIZE,
				true
			));
	}
	
	function validate_password($password, $correct_hash)
	{
		$params = explode(":", $correct_hash);
		if(count($params) < HASH_SECTIONS)
		   return false;
		$pbkdf2 = base64_decode($params[HASH_PBKDF2_INDEX]);
		return slow_equals(
			$pbkdf2,
			pbkdf2(
				$params[HASH_ALGORITHM_INDEX],
				$password,
				$params[HASH_SALT_INDEX],
				(int)$params[HASH_ITERATION_INDEX],
				strlen($pbkdf2),
				true
			)
		);
	}
	
	// Compares two strings $a and $b in length-constant time.
	function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}
	
	/*
	 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 * $algorithm - The hash algorithm to use. Recommended: SHA256
	 * $password - The password.
	 * $salt - A salt that is unique to the password.
	 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
	 * $key_length - The length of the derived key in bytes.
	 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
	 * Returns: A $key_length-byte key derived from the password and salt.
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 */
	function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
	{
		$algorithm = strtolower($algorithm);
		if(!in_array($algorithm, hash_algos(), true))
			trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
		if($count <= 0 || $key_length <= 0)
			trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
	
		if (function_exists("hash_pbkdf2")) {
			// The output length is in NIBBLES (4-bits) if $raw_output is false!
			if (!$raw_output) {
				$key_length = $key_length * 2;
			}
			return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
		}
	
		$hash_length = strlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);
	
		$output = "";
		for($i = 1; $i <= $block_count; $i++) {
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
			}
			$output .= $xorsum;
		}
	
		if($raw_output)
			return substr($output, 0, $key_length);
		else
			return bin2hex(substr($output, 0, $key_length));
	}	
?>

