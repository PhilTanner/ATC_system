<?php
	require_once "atc.class.php";
	
	class ATC_Documentation extends ATC
	{
		public function get_cadets_enrolled_on_date( $date )
		{
			$query = '
				SELECT	COUNT(DISTINCT `personnel`.`personnel_id`),
						`personnel`.`is_female`
				FROM 	`personnel`
				WHERE 	`personnel`.`joined_date` <= "'.date('Y-m-d', $date).'" 
					AND (`personnel`.`left_date` >= "'.date('Y-m-d', $date).'" OR `personnel`.`left_date` IS NULL)
				GROUP BY `personnel`.`is_female`';

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
		
		public function nzcf20_stats( $year, $month )
		{
			$firstdayofmonth = strtotime( $year.'-'.$month.'-01');
			$lastdayofmonth = strtotime( $year.'-'.$month.'-'.cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year) ); 
			
			var_dump(date("c", $firstdayofmonth)); var_dump(date("c",$lastdayofmonth));
			
			var_dump( self::get_cadets_enrolled_on_date( $lastdayofmonth ));
		}
		
		
	}
	
?>