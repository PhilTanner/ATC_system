<?php
	/* Location/Regional settings */
	setlocale(LC_ALL, 'en_NZ.UTF-8');
	date_default_timezone_set('Pacific/Auckland');
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
	
	/* Rank level permission setttings */		
	define( 'ATC_USER_LEVEL_CADET',			0 );
	define( 'ATC_USER_LEVEL_NCO', 				ATC_USER_LEVEL_CADET | ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_SUPOFF', 			ATC_USER_LEVEL_CADET | ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_ATTENDANCE_VIEW | ATC_PERMISSION_ACTIVITIES_VIEW | ATC_PERMISSION_LOCATIONS_VIEW );
	define( 'ATC_USER_LEVEL_OFFICER', 			ATC_USER_LEVEL_CADET | ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_ATTENDANCE_VIEW | ATC_PERMISSION_ACTIVITIES_VIEW | ATC_PERMISSION_LOCATIONS_VIEW | ATC_PERMISSION_FINANCE_VIEW | ATC_PERMISSION_STORES_VIEW | ATC_PERMISSION_TRAINING_VIEW );
	
	/* Specific Officer roles */
	define( 'ATC_USER_LEVEL_ADJUTANT', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_PERSONNEL_EDIT | ATC_PERMISSION_ATTENDANCE_EDIT | ATC_PERMISSION_ACTIVITIES_EDIT | ATC_PERMISSION_FINANCE_EDIT | ATC_PERMISSION_LOCATIONS_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT);
	define( 'ATC_USER_LEVEL_STORES', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_STORES_EDIT );
	define( 'ATC_USER_LEVEL_TRAINING', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_LOCATIONS_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT | ATC_PERMISSION_TRAINING_EDIT);
	define( 'ATC_USER_LEVEL_CUCDR', 			ATC_USER_LEVEL_OFFICER | ATC_PERMISSION_PERSONNEL_EDIT | ATC_PERMISSION_ACTIVITY_TYPE_EDIT );
	
	/* Unit Support Committee roles */
	define( 'ATC_USER_LEVEL_USC', 				ATC_USER_LEVEL_OFFICER );
	define( 'ATC_USER_LEVEL_TREASURER',			ATC_USER_LEVEL_USC | ATC_PERMISSION_FINANCE_EDIT );
	
	define( 'ATC_USER_LEVEL_EMRG_CONTACT', 		ATC_PERMISSION_PERSONNEL_VIEW | ATC_PERMISSION_ACTIVITIES_VIEW | ATC_PERMISSION_LOCATIONS_VIEW );
	
?>
