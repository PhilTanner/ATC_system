<?php
	/* Location/Regional settings */
	setlocale(LC_ALL, 'en_NZ.UTF-8');
	define( 'ATC_TZ_DEFAULT', 'Pacific/Auckland' );
	
	date_default_timezone_set(ATC_TZ_DEFAULT);
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
	{
		// http://msdn.microsoft.com/en-us/library/cdax410z%28v=vs.90%29.aspx
		setlocale(LC_ALL, 'enz');
	}
	
	/* Database connection settngs */	
	define( 'ATC_SETTING_DB_USER', 'atc' );
	define( 'ATC_SETTING_DB_PSWD', 'ZIERIESs5ESa' );
	define( 'ATC_SETTING_DB_HOST', 'localhost' );
	define( 'ATC_SETTING_DB_NAME', 'atc' );
	
	/* Presentation settings */
	define( 'ATC_SETTING_PARADE_NIGHT',			"Wednesday" );
	define( 'ATC_SETTING_DATETIME_INPUT',         "Y-m-d\TH:i");
	define( 'ATC_SETTING_DATETIME_OUTPUT',         "j M, H:i");
	define( 'ATC_SETTING_DATE_INPUT',         "Y-m-d");
	define( 'ATC_SETTING_DATE_OUTPUT',         "j M");
	define( 'ATC_SETTING_FULL_DISPLAY_NAME',		'CONCAT("RNK, ", `personnel`.`lastname`,", ",`personnel`.`firstname`)' );
	define( 'ATC_SETTING_DISPLAY_NAME',		'CONCAT(`personnel`.`lastname`,", ",`personnel`.`firstname`)' );
	define( 'ATC_SETTING_DISPLAY_RANK_SHORTNAME', '( SELECT `rank_shortname` FROM `personnel_rank` INNER JOIN `rank` ON `rank`.`rank_id` = `personnel_rank`.`rank_id` WHERE `personnel_rank`.`personnel_id` = `personnel`.`personnel_id` ORDER BY `date_achieved` DESC LIMIT 1 )');
	
	/* Money settings */
	define( 'ATC_SETTING_FINANCE_TERM_FEES',			50.00 );
	define( 'ATC_SETTING_FINANCE_UNIFORM_DEPOSIT',		50.00 );
	define( 'ATC_SETTING_FINANCE_MONEYFORMAT', 	'%(#2.2n' );
	
	define( 'ATC_DRESS_CODE_BLUES_NAME',				'No 6 GP Uniform' );
	define( 'ATC_DRESS_CODE_DPM_NAME',				'DPM' );
	define( 'ATC_DRESS_CODE_BLUES_AND_DPM_NAME',	'Mixed' );
	define( 'ATC_DRESS_CODE_MUFTI_NAME',			'Mufti' );
	
	define( 'ATC_ATTENDANCE_PRESENT_SYMBOL',		"X" );
	define( 'ATC_ATTENDANCE_ON_LEAVE_SYMBOL',		"L" );
	define( 'ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE_SYMBOL',	"o" );
		
	/* Rank level permission setttings */		
	define( 'ATC_USER_LEVEL_CADET',			  ATC_PERMISSION_TRAINING_VIEW | ATC_PERMISSION_ACTIVITIES_VIEW );
	define( 'ATC_USER_LEVEL_JNCO', 				ATC_USER_LEVEL_CADET | ATC_PERMISSION_TRAINING_VIEW );
	define( 'ATC_USER_LEVEL_SNCO', 				ATC_USER_LEVEL_JNCO | ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_LOCATIONS_VIEW);
	define( 'ATC_USER_LEVEL_SUPOFF', 			ATC_USER_LEVEL_SNCO | ATC_PERMISSION_ATTENDANCE_VIEW );
	define( 'ATC_USER_LEVEL_OFFICER', 			ATC_USER_LEVEL_SUPOFF | ATC_PERMISSION_FINANCE_VIEW | ATC_PERMISSION_STORES_VIEW | ATC_PERMISSION_SYSTEM_VIEW );
	
	/* Specific Officer roles */
	define( 'ATC_USER_LEVEL_ADJUTANT', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_PERSONNEL_EDIT | ATC_PERMISSION_ATTENDANCE_EDIT | ATC_PERMISSION_ACTIVITIES_EDIT | ATC_PERMISSION_FINANCE_EDIT | ATC_PERMISSION_LOCATIONS_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT);
	define( 'ATC_USER_LEVEL_STORES', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_STORES_EDIT );
	define( 'ATC_USER_LEVEL_TRAINING', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_LOCATIONS_EDIT | ATC_PERMISSION_ACTIVITIES_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT | ATC_PERMISSION_TRAINING_EDIT);
	define( 'ATC_USER_LEVEL_CUCDR', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_PERSONNEL_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT );
	
	/* Unit Support Committee roles */
	define( 'ATC_USER_LEVEL_USC', 				ATC_USER_LEVEL_OFFICER & ~ATC_PERMISSION_STORES_VIEW & ~ATC_PERMISSION_LOCATIONS_VIEW);
	define( 'ATC_USER_LEVEL_TREASURER',			ATC_USER_LEVEL_USC | ATC_PERMISSION_FINANCE_EDIT );
	
	define( 'ATC_USER_LEVEL_EMRG_CONTACT', 		ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_ACTIVITIES_VIEW | ATC_PERMISSION_LOCATIONS_VIEW );
	
?>
