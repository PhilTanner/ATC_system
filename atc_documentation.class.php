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
			
			return $returnval;
		}
		
		
	}
	
?>