<?php
	require_once "atc.class.php";
	
	class ATC_Documentation extends ATC
	{
		public function get_cadets_enrolled_on_date( $date )
		{
			$query = '
				SELECT	COUNT(DISTINCT `personnel`.`personnel_id`) as `count`,
						`personnel`.`is_female`,
						( 
   						SELECT `nzcf20_order` 
   						FROM `personnel_rank` 
								INNER JOIN `rank` 
									ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   						WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   							AND (`nzcf20_order` > 0 AND `nzcf20_order` < 15 AND `nzcf20_order` IS NOT NULL)
   						ORDER BY `date_achieved` DESC 
   						LIMIT 1 
						) AS `nzcf_order` 
				FROM 	`personnel`
				WHERE 	`personnel`.`joined_date` <= "'.date('Y-m-d', $date).'" 
					AND (`personnel`.`left_date` >= "'.date('Y-m-d', $date).'" OR `personnel`.`left_date` IS NULL)
				GROUP BY `personnel`.`is_female`, `nzcf_order`
				ORDER BY `nzcf_order`';

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
		
		public function get_cadet_attendance( $startdate, $enddate )
		{
			$query = '
				SELECT	COUNT(DISTINCT `personnel`.`personnel_id`) as `count`
				FROM 	`personnel`
                		INNER JOIN `attendance_register`
                        	ON `attendance_register`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 	`attendance_register`.`date` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'"
					AND `attendance_register`.`presence` = '.ATC_ATTENDANCE_PRESENT.'
					AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_CADETS.')
				GROUP BY `attendance_register`.`date`
				ORDER BY `attendance_register`.`date`';

			$attendance = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$attendance[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			
			return $attendance;
		}
		
		public function get_officer_attendance( $startdate, $enddate, $supplimentary=false )
		{
			$query = '
				SELECT	`personnel`.`personnel_id`,
					`personnel`.`firstname`,
					`personnel`.`lastname`,
					`personnel`.`access_rights`,
					`personnel`.`is_female`,
					( 
   					SELECT `ordering` 
   					FROM `personnel_rank` 
							INNER JOIN `rank` 
								ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   					WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   					ORDER BY `date_achieved` DESC 
   					LIMIT 1 
					) AS `rank_order`,
					( 
   					SELECT `rank_shortname` 
   					FROM `personnel_rank` 
							INNER JOIN `rank` 
								ON `rank`.`rank_id` = `personnel_rank`.`rank_id` 
   					WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` 
   					ORDER BY `date_achieved` DESC 
   					LIMIT 1 
					) AS `rank`,
					COUNT(DISTINCT `attendance_register`.`date`) as `parades`,
					COUNT(DISTINCT `attendance_register`.`date`)*3 as `parade_hours`,
					(
						SELECT SUM( HOUR( TIMEDIFF( `activity`.`startdate`, `activity`.`enddate`) ) )
						FROM `activity`
							INNER JOIN `activity_register`
								ON `activity`.`activity_id` = `activity_register`.`activity_id`
						WHERE `activity_register`.`personnel_id` = `personnel`.`personnel_id`
							AND `activity_register`.`presence` = '.ATC_ATTENDANCE_PRESENT.'
							AND `activity`.`startdate` >= "'.date('Y-m-d', $startdate).'" 
							AND `activity`.`enddate` <= "'.date('Y-m-d', $enddate).' 23:59:59"
							AND HOUR( TIMEDIFF( `activity`.`startdate`, `activity`.`enddate`) ) < 6
					) AS `activity_hours`,
					(
						SELECT COUNT( HOUR( TIMEDIFF( `activity`.`startdate`, `activity`.`enddate`) ) )
						FROM `activity`
							INNER JOIN `activity_register`
								ON `activity`.`activity_id` = `activity_register`.`activity_id`
						WHERE `activity_register`.`personnel_id` = `personnel`.`personnel_id`
							AND `activity_register`.`presence` = '.ATC_ATTENDANCE_PRESENT.'
							AND `activity`.`startdate` >= "'.date('Y-m-d', $startdate).'" 
							AND `activity`.`enddate` <= "'.date('Y-m-d', $enddate).' 23:59:59"
							AND HOUR( TIMEDIFF( `activity`.`startdate`, `activity`.`enddate`) ) >= 6
					) AS `activity_days`
							
				FROM 	`personnel`
                		LEFT JOIN `attendance_register`
                        	ON `attendance_register`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 	`attendance_register`.`date` BETWEEN "'.date('Y-m-d', $startdate).'" AND "'.date('Y-m-d', $enddate).'"
					AND `attendance_register`.`presence` = '.ATC_ATTENDANCE_PRESENT.'
					AND `personnel`.`access_rights` IN ('.ATC_USER_GROUP_OFFICERS.')
				GROUP BY `personnel`.`personnel_id`
				HAVING '.($supplimentary?'':'NOT ').'`rank` = \'CDT\'
				ORDER BY `rank_order`, `lastname`, `firstname`';

			$attendance = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$attendance[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);
			
			return $attendance;
		}
		
		public function nzcf20_stats( $year, $month )
		{
			$firstdayofmonth = strtotime( $year.'-'.$month.'-01');
			$lastdayofmonth = strtotime( $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year) ); 
			
			// We'll send back an array of sections with the right data in them
			$returnval = array();
			
			$section1 = array( array( '', '' ), array( '', '' ), array( '', '' ), array( '', '' ), array( '', '' ), array( '', '' ), array( '', '' ) );
			$enrolments = self::get_cadets_enrolled_on_date( $lastdayofmonth );
			// Section one needs some munging to get what we want out
			foreach($enrolments as $obj)
			{		
				if( $obj->nzcf_order > 0 && $obj->nzcf_order < 15 )
				{
					$section1[(($obj->nzcf_order)-1)][(($obj->is_female)+1)] = $obj->count;
					$section1[6][(($obj->is_female)+1)] += $obj->count;
				}
			}
			$returnval[] = $section1;
			
			// Section 2 is much easier
			$returnval[] = self::get_cadet_attendance( $firstdayofmonth, $lastdayofmonth );
			
			// As is 3
			$returnval[] = self::get_officer_attendance( $firstdayofmonth, $lastdayofmonth );
			
			// And 4
			
			$returnval[] = self::get_officer_attendance( $firstdayofmonth, $lastdayofmonth, true );
			
			// Section 5, we only want a month of activities
			$days = (($lastdayofmonth - $firstdayofmonth)/60/60/24)+1;
			$returnval[] = self::get_activities( date('Y-m-d', $firstdayofmonth), (int)$days );
			
			// Section 6 - more complicated, need to do the next month upcoming, but need to account for changing days in month (dont want to end on 28 Mar)
			$startdate = strtotime("+1 month", $firstdayofmonth);
			$days = ((strtotime("+2 months", $firstdayofmonth) - $startdate)/60/60/24);
			$returnval[] = self::get_activities( date('Y-m-d', $startdate), (int)$days );
			
			return $returnval;
		}
		
		
	}
	
?>