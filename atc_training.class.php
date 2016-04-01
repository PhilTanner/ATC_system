<?php
	require_once "atc.class.php";
	
	class ATC_Training extends ATC
	{
		
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
					`title` ASC;';

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
					`category` ASC;';

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
		}
		
		public function set_lesson( $id, $lesson_category_id, $code, $title, $nzqa_qualifies, $level, $nzcf )
		{
			// Need system permissions to add/edit lessons themselves, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strlen(trim($code)) )
				throw new ATCExceptionBadData('Invalid lesson code');
			if( !strlen(trim($title)) )
				throw new ATCExceptionBadData('Invalid lesson title');

			$foo = self::$mysqli->insert_id;
			$query = '
				INSERT INTO `lesson` (
					`lesson_id`, 
					`lesson_category_id`,
					`code`,
					`title`,
					`nzqa_qualifies`,
					`level`,
					`nzcf`
				) VALUES (
					'.(int)$id.',
					'.(int)$lesson_category_id.', 
					"'.self::$mysqli->real_escape_string($code).'", 
					"'.self::$mysqli->real_escape_string($title).'", 
					'.(int)$nzqa_qualifies.',
					'.(int)$level.',
					'.(int)$nzcf.'
				) ON DUPLICATE KEY UPDATE 
					`lesson_category_id` = '.(int)$lesson_category_id.', 
					`code` = "'.self::$mysqli->real_escape_string($code).'", 
					`title` = "'.self::$mysqli->real_escape_string($title).'", 
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
		
		public function set_lesson_category( $id, $category, $color, $short, $suggested_group, $suggested_nzcf )
		{
			// Need system permissions to add/edit lesson categories, TRAINING to add resources or set the schedule
			if(!self::user_has_permission( ATC_PERMISSION_SYSTEM_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to edit this object");
			if( !strlen(trim($category)) )
				throw new ATCExceptionBadData('Invalid lesson category name');
			if( !strlen(trim($color)) )
				throw new ATCExceptionBadData('Invalid lesson category colour');
			if( !strlen(trim($short)) )
				throw new ATCExceptionBadData('Invalid lesson category short name');

			$foo = self::$mysqli->insert_id;
			$query = '
				INSERT INTO `lesson_category` (
					`lesson_category_id`,
					`category`,
					`colour`,
					`category_short`,
					`suggested_group`,
					`suggested_nzcf`
				) VALUES (
					'.(int)$id.',
					"'.self::$mysqli->real_escape_string($category).'", 
					"'.self::$mysqli->real_escape_string($color).'", 
					"'.self::$mysqli->real_escape_string($short).'", 
					'.(int)$suggested_group.', 
					'.(int)$suggested_nzcf.'
				) ON DUPLICATE KEY UPDATE 
					`category` = "'.self::$mysqli->real_escape_string($category).'", 
					`colour` = "'.self::$mysqli->real_escape_string($color).'", 
					`category_short` = "'.self::$mysqli->real_escape_string($short).'",
					`suggested_group` = '.(int)$suggested_group.', 
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
		
		
	}
	
?>