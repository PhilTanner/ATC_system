<?php
	require_once "atc.class.php";
	
	class ATC_Training extends ATC
	{
		public function clear_timetable_slot( $startdate, $enddate, $group )
		{
			// Need system permissions to add/edit lesson categories, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid startdate');
			if( !strtotime($enddate) || (strtotime($startdate) > strtotime($enddate)) )
				throw new ATCExceptionBadData('Invalid enddate');
				
			if( !$result = self::$mysqli->query('
				DELETE FROM 
					`lesson_timetable` 
				WHERE
					`group` = '.(int)$group.' 
					AND `startdate` = "'.date( 'Y-m-d H:i', strtotime($startdate)).'"
					AND `enddate` = "'.date( 'Y-m-d H:i', strtotime($enddate)).'";') )
				throw new ATCExceptionDBError(self::$mysqli->error);
				
			return true;
		}
		
		public function delete_lesson_from_timetable( $lesson_id, $year )
		{
			// Need system permissions to add/edit lesson categories, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
				
			if( !$result = self::$mysqli->query('
				DELETE FROM 
					`lesson_timetable` 
				WHERE
					`lesson_id` = '.(int)$lesson_id.'
					AND `startdate` BETWEEN "'.(int)$year.'-01-01" AND  "'.(int)$year.'-12-31";') )
				throw new ATCExceptionDBError(self::$mysqli->error);
				
			return true;
		}
		
		public function get_lesson($id=null)
		{
			if( !self::user_has_permission(ATC_PERMISSION_TRAINING_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
				SELECT
					*
				FROM
					`lesson`
					INNER JOIN `lesson_category`
						ON `lesson`.`lesson_category_id` = `lesson_category`.`lesson_category_id`
				'.(is_null($id)?'':'WHERE `lesson_id`='.(int)$id).'
				ORDER BY
					`category_short`, `code` ASC;';

			$return = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$return[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $return;
		}
		
		public function get_lesson_category($id=null)
		{
			if( !self::user_has_permission(ATC_PERMISSION_TRAINING_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
				SELECT
					*
				FROM
					`lesson_category`
				'.(is_null($id)?'':'WHERE `lesson_category_id`='.(int)$id).'
				ORDER BY
					`category_short` ASC;';

			$return = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$return[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $return;
		}
		
		public function get_timetable( $startdate, $enddate )
		{
			if( !self::user_has_permission(ATC_PERMISSION_TRAINING_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$startdate = (is_null($startdate)?null:strtotime($startdate));
			$enddate = (is_null($enddate)?null:strtotime($enddate));
			
			$query = '
				SELECT
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					`personnel`.`personnel_id`,
					`location`.`name`,
					`location`.`location_id`,
					`lesson_timetable`.`startdate`,
					`lesson_timetable`.`lesson_id`,
					`lesson_timetable`.`group`,
					`lesson_timetable`.`dress_code`
				FROM
					`lesson_timetable`
					INNER JOIN `personnel`
						ON `lesson_timetable`.`personnel_id` = `personnel`.`personnel_id`
					INNER JOIN `location`
						ON `lesson_timetable`.`location_id` = `location`.`location_id`
				WHERE 1=1
					'. (is_null($startdate)?'':' AND `startdate` >= "'.date('Y-m-d',$startdate)).'"
					'. (is_null($enddate)?'':' AND `enddate` <= "'.date('Y-m-d',$enddate)).'"
				ORDER BY
					`startdate` ASC;';

			$return = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$return[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $return;
		}
		
		public function set_lesson( $id, $lesson_category_id, $code, $description, $dress_code, $nzqa_qualifies, $level, $nzcf )
		{
			// Need system permissions to add/edit lessons themselves, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strlen(trim($code)) )
				throw new ATCExceptionBadData('Invalid lesson code');
			if( !strlen(trim($description)) )
				throw new ATCExceptionBadData('Invalid lesson description');
			if( $dress_code != ATC_DRESS_CODE_BLUES && $dress_code != ATC_DRESS_CODE_DPM && $dress_code != ATC_DRESS_CODE_BLUES_AND_DPM && $dress_code != ATC_DRESS_CODE_MUFTI )
				throw new ATCExceptionBadData('Unknown dress code value');

			$foo = self::$mysqli->insert_id;
			$query = '
				INSERT INTO `lesson` (
					`lesson_id`, 
					`lesson_category_id`,
					`code`,
					`description`,
					`dress_code`,
					`nzqa_qualifies`,
					`level`,
					`nzcf`
				) VALUES (
					'.(int)$id.',
					'.(int)$lesson_category_id.', 
					"'.self::$mysqli->real_escape_string($code).'", 
					"'.self::$mysqli->real_escape_string($description).'", 
					'.(int)$dress_code.',
					'.(int)$nzqa_qualifies.',
					'.(int)$level.',
					'.(int)$nzcf.'
				) ON DUPLICATE KEY UPDATE 
					`lesson_category_id` = '.(int)$lesson_category_id.', 
					`code` = "'.self::$mysqli->real_escape_string($code).'", 
					`description` = "'.self::$mysqli->real_escape_string($description).'", 
					`dress_code` = '.(int)$dress_code.',
					`nzqa_qualifies` = '.(int)$nzqa_qualifies.',
					`level` = '.(int)$level.',
					`nzcf` = '.(int)$nzcf.';';
			if ($result = self::$mysqli->query($query))
			{
				$bar = self::$mysqli->insert_id;
				if( !$id && $bar != $foo )
					$id = $bar;
					
				self::log_action( 'lesson', $query, $id );
			} else 
				throw new ATCExceptionDBError(self::$mysqli->error);
				
			return $id;
		}
		
		public function set_lesson_category( $id, $category, $color, $textcolor, $short, $suggested_nzcf )
		{
			// Need system permissions to add/edit lesson categories, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strlen(trim($category)) )
				throw new ATCExceptionBadData('Invalid lesson category name');
			if( strlen(trim($color)) != 7 )
				throw new ATCExceptionBadData('Invalid lesson category colour');
			if( strlen(trim($textcolor)) != 7 )
				throw new ATCExceptionBadData('Invalid lesson category text colour');
			if( !strlen(trim($short)) )
				throw new ATCExceptionBadData('Invalid lesson category short name');

			$foo = self::$mysqli->insert_id;
			$query = '
				INSERT INTO `lesson_category` (
					`lesson_category_id`,
					`category`,
					`colour`,
					`text_colour`,
					`category_short`,
					`suggested_nzcf`
				) VALUES (
					'.(int)$id.',
					"'.self::$mysqli->real_escape_string($category).'", 
					"'.self::$mysqli->real_escape_string($color).'", 
					"'.self::$mysqli->real_escape_string($textcolor).'", 
					"'.self::$mysqli->real_escape_string($short).'", 
					'.(int)$suggested_nzcf.'
				) ON DUPLICATE KEY UPDATE 
					`category` = "'.self::$mysqli->real_escape_string($category).'", 
					`colour` = "'.self::$mysqli->real_escape_string($color).'", 
					`text_colour` = "'.self::$mysqli->real_escape_string($textcolor).'", 
					`category_short` = "'.self::$mysqli->real_escape_string($short).'",
					`suggested_nzcf` = '.(int)$suggested_nzcf.';';
			if ($result = self::$mysqli->query($query))
			{
				$bar = self::$mysqli->insert_id;
				if( !$id && $bar != $foo )
					$id = $bar;
					
				self::log_action( 'lesson_category', $query, $id );
			} else 
				throw new ATCExceptionDBError(self::$mysqli->error);
				
			return $id;
		}
		
		public function set_timetable( $lesson_id, $location_id, $personnel_id, $startdate, $enddate, $group, $dress_code )
		{
			// Need system permissions to add/edit lesson categories, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid startdate');
			if( !strtotime($enddate) || (strtotime($startdate) > strtotime($enddate)) )
				throw new ATCExceptionBadData('Invalid enddate');
			if( $dress_code != ATC_DRESS_CODE_BLUES && $dress_code != ATC_DRESS_CODE_DPM && $dress_code != ATC_DRESS_CODE_BLUES_AND_DPM && $dress_code != ATC_DRESS_CODE_MUFTI )
				throw new ATCExceptionBadData('Unknown dress code value');
			
			// Clear our block before inserting our new one.
			self::clear_timetable_slot( $startdate, $enddate, $group );
			self::delete_lesson_from_timetable( $lesson_id, date('Y', strtotime($startdate)) );
			
			$foo = self::$mysqli->insert_id;
			$query = '
				INSERT INTO `lesson_timetable` (
					`lesson_id`,
					`location_id`,
					`personnel_id`,
					`startdate`,
					`enddate`,
					`group`,
					`dress_code`
				) VALUES (
					'.(int)$lesson_id.',
					'.(int)$location_id.',
					'.(int)$personnel_id.',
					"'.date( 'Y-m-d H:i', strtotime($startdate)).'",
					"'.date( 'Y-m-d H:i', strtotime($enddate)).'",
					'.(int)$group.',
					'.(int)$dress_code.'
				) ON DUPLICATE KEY UPDATE 
					`lesson_id` = '.(int)$lesson_id.', 
					`location_id` = '.(int)$location_id.', 
					`personnel_id` = '.(int)$personnel_id.', 
					`startdate` = "'.date( 'Y-m-d H:i', strtotime($startdate)).'",
					`enddate` = "'.date( 'Y-m-d H:i', strtotime($enddate)).'", 
					`group` = '.(int)$group.',
					`dress_code` = '.(int)$dress_code.';';
			if ($result = self::$mysqli->query($query))
			{
				$bar = self::$mysqli->insert_id;
				if( !$lesson_id && $bar != $foo )
					$lesson_id = $bar;
					
				self::log_action( 'lesson_category', $query, $lesson_id );
			} else 
				throw new ATCExceptionDBError(self::$mysqli->error);
				
			return $lesson_id;
		}
	}
	
?>