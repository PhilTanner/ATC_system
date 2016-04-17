<?php
	require_once "atc_finance.class.php";
	$ATC = new ATC_Finance();
	
	$CRLF = "\r\n";
	
	try {
		$ATC->check_user_session( $_GET['key'], ATC_SESSION_TYPE_CALENDAR );
		$activities = $ATC->get_activities();
		$mydetails = $ATC->get_personnel($ATC->get_currentuser_id());
		
		$users = array();
		$users[$ATC->get_currentuser_id()] = $mydetails;
		
	} catch (ATCExceptionInvalidUserSession $e) {
		if(substr($_SERVER['SCRIPT_NAME'], -9, 9) != "login.php" )
			header('Location: login.php', true, 302);
	}
	
	header('Content-type: text/calendar');
	//header('Content-type: text/text');
	
	echo "BEGIN:VCALENDAR".$CRLF;
	echo "VERSION:2.0".$CRLF;
	echo "METHOD:PUBLISH".$CRLF;
	echo "PRODID:-//github.com/PhilTanner/ATC_system//Activities Calendar- 49sqn.philtanner.com//EN".$CRLF;

	foreach( $activities as $obj )
	{
		echo 'BEGIN:VEVENT'.$CRLF;
		// Use gmdate() and append 'Z' to always output in UTC, so we don't worry about daylight savings, or anyone's timezone being wrong...
		echo 'DTSTART:'.gmdate("Ymd\THis\Z", strtotime($obj->startdate)).$CRLF;
		echo 'DTEND:'.gmdate("Ymd\THis\Z", strtotime($obj->enddate)).$CRLF;
		echo 'SUMMARY:'.vcalendarsafestring($obj->type).' - '.vcalendarsafestring($obj->title).$CRLF;
		echo 'LOCATION:'.vcalendarsafestring($obj->location_name).' '.vcalendarsafestring($obj->address).$CRLF;
		echo 'UID:'.dechex($obj->activity_id).'@49sq.philtanner.com'.$CRLF;
		echo 'CONTACT:'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;
		echo 'ATTENDEE;ROLE=CHAIR;'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;
		echo 'ORGANIZER;'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;
		
		$emergencycontactdetails = vcalendaruserstring( $obj->twoic_display_name, $obj->twoic_email, $obj->twoic_mobile_phone);
		if( strlen(trim($emergencycontactdetails)) > 1 )
			echo 'ATTENDEE;ROLE=NON-PARTICIPANT;'.$emergencycontactdetails.$CRLF;
		
		$userids = explode(',', $obj->attendees );
		$description = '';
		foreach($userids as $userid )
		{
			if( !$userid ) continue;
			
			// Don't keep repulling the same user details for every event, if we find them once, store them for next time...
			if( !isset($users[$userid]) )
				$users[$userid] = $ATC->get_personnel($userid);
			
			if( !$ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW, $userid) )
			{
				echo 'ATTENDEE;ROLE=REQ-PARTICIPANT;'.vcalendaruserstring( $users[$userid]->rank.' '.$users[$userid]->display_name, '', '').$CRLF;
				$description .= '  o '.$users[$userid]->rank.' '.$users[$userid]->display_name.$CRLF;
			} else {
				echo 'ATTENDEE;ROLE=REQ-PARTICIPANT;'.vcalendaruserstring( $users[$userid]->rank.' '.$users[$userid]->display_name, $users[$userid]->email, $users[$userid]->mobile_phone).$CRLF;
				$description .= '  o '.$users[$userid]->rank.' '.$users[$userid]->display_name.' ('.$users[$userid]->mobile_phone.')'.$CRLF.'    <'.$users[$userid]->email.'>'.$CRLF;
			}
			
			
		}
		if( strlen(trim($description)) )
		{
			$description = 'Cost: '.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $obj->cost).$CRLF.'Attendees:'.$CRLF.$description;
			echo 'DESCRIPTION:'.wordwrap($description, 75, "\n  ", true).$CRLF;
		}
		
		echo 'END:VEVENT'.$CRLF;
		
		//echo 'CONTACT:'.vcalendarsafestring($obj->display_name).'\\,'.vcalendarsafestring($obj->mobile_phone).$CRLF;
		//echo 'SEQUENCE:0'.$CRLF;
		//echo 'DURATION:PT3H0M'.$CRLF;
		//echo 'ORGANIZER;CN='.$obj->display_name.':mailto:'.$obj->email.$CRLF;
		//echo 'ATTENDEE;ROLE=NON-PARTICIPANT:'.vcalendaruserstring( $obj->twoic_display_name, $obj->twoic_email, $obj->twoic_mobile_phone).$CRLF;
		//echo 'ATTENDEE;ROLE=CHAIR:mailto:'.$obj->email.$CRLF;
	}

	echo "END:VCALENDAR".$CRLF;
	
	function vcalendarsafestring($string)
	{
		return str_replace(",", "\\,", $string);
	}
	
	function vcalendaruserstring( $name, $email, $mobile )
	{
		$string = '';
		if(strlen(trim($name))> 1)
			$string .= 'CN="'.$name.'"';
		if(strlen(trim($email)))
			$string .=' :MAILTO:'.$email;
		if(strlen(trim($mobile)))
			$string .=' :TEL:+64-'.substr($mobile, 1);
		
		return $string;
	}
	
	exit();
	?>
