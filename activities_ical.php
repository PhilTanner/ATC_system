<?php
	require_once "atc_finance.class.php";
	$ATC = new ATC_Finance();
	
	$CRLF = "\r\n";
	
	try {
		if( $ATC->check_user_session( $_GET['key'], ATC_SESSION_TYPE_CALENDAR ) )
		{
			$ATC->become_user_from_session($_GET['key']);
			$activities = $ATC->get_activities();
			$mydetails = $ATC->get_personnel($ATC->get_currentuser_id());
		
			$users = array();
			$users[$ATC->get_currentuser_id()] = $mydetails;
		}
	} catch (ATCExceptionInvalidUserSession $e) {
		if(substr($_SERVER['SCRIPT_NAME'], -9, 9) != "login.php" )
			header('Location: login.php', true, 302);
	}
	
	header('Content-type: text/calendar');
	header('Content-Disposition:inline; filename=49squadron_activities.ics');
	//header('Content-type: text/text');
	//header("Content-Disposition:inline;filename=49squadron_activities.ics");
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	
	echo "BEGIN:VCALENDAR".$CRLF;
	echo "VERSION:2.0".$CRLF;
	echo "METHOD:PUBLISH".$CRLF;
	echo "PRODID:-//github.com/PhilTanner/ATC_system//Activities Calendar- 49sqn.philtanner.com//EN".$CRLF;

	foreach( $activities as $obj )
	{
		echo 'BEGIN:VEVENT'.$CRLF;
		// Use gmdate() and append 'Z' to always output in UTC, so we don't worry about daylight savings, or anyone's timezone being wrong...
		echo 'DTSTAMP:'.gmdate("Ymd\THis\Z", strtotime($obj->startdate)).$CRLF;
		echo 'DTSTART:'.gmdate("Ymd\THis\Z", strtotime($obj->startdate)).$CRLF;
		echo 'DTEND:'.gmdate("Ymd\THis\Z", strtotime($obj->enddate)).$CRLF;
		echo 'SUMMARY:'.vcalendarsafestring($obj->type).' - '.vcalendarsafestring($obj->title).$CRLF;
		echo 'LOCATION:'.vcalendarsafestring($obj->location_name).' '.vcalendarsafestring($obj->address).$CRLF;
		echo 'UID:'.dechex($obj->activity_id).'@49sq.philtanner.com'.$CRLF;
		echo 'CONTACT:'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;
		echo 'ATTENDEE;ROLE=CHAIR;'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;
		echo 'ORGANIZER;'.vcalendaruserstring( $obj->rank.' '.$obj->display_name, $obj->email, $obj->mobile_phone).$CRLF;

		// Set up our reminders for paperwork due dates
		if( strlen(trim($obj->nzcf12_to_cucdr)) && strtotime($obj->nzcf12_to_cucdr) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf12_to_cucdr)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF12s due to CUCDR').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}
		if( strlen(trim($obj->nzcf11_to_cucdr)) && strtotime($obj->nzcf11_to_cucdr) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf11_to_cucdr)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF11s due to CUCDR').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}
		if( strlen(trim($obj->nzcf12_to_hq)) && strtotime($obj->nzcf12_to_hq) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf12_to_hq)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF12s due to CFTSU').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}
		if( strlen(trim($obj->nzcf11_to_hq)) && strtotime($obj->nzcf11_to_hq) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf11_to_hq)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF11s due to CFTSU').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}
		if( strlen(trim($obj->nzcf8_issued)) && strtotime($obj->nzcf8_issued) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf8_issued)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF8s to CDT').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}
		if( strlen(trim($obj->nzcf8_return)) && strtotime($obj->nzcf8_return) ){
			echo 'BEGIN:VALARM'.$CRLF;
			echo 'TRIGGER;VALUE=DATE-TIME:'.gmdate("Ymd\THis\Z", strtotime($obj->nzcf12_to_cucdr)).$CRLF;
			echo 'ACTION:DISPLAY'.$CRLF;
			echo 'DESCRIPTION:'.vcalendarsafestring($obj->title).' ('.gmdate("d/m/Y", strtotime($obj->startdate)).'): '.vcalendarsafestring('NZCF8s due back').$CRLF;
			echo 'END:VALARM'.$CRLF;
		}


		
		$emergencycontactdetails = vcalendaruserstring( $obj->twoic_display_name, $obj->twoic_email, $obj->twoic_mobile_phone);
		if( strlen(trim($emergencycontactdetails)) > 1 )
			echo 'ATTENDEE;ROLE=NON-PARTICIPANT;'.$emergencycontactdetails.$CRLF;
		
		$userids = explode(',', $obj->attendees );
		$description = '';
		$htmldesc = '<ul>';
		foreach($userids as $userid )
		{
			if( !$userid ) continue;
			
			// Don't keep repulling the same user details for every event, if we find them once, store them for next time...
			if( !isset($users[$userid]) )
				$users[$userid] = $ATC->get_personnel($userid);
			
			if( !$ATC->user_has_permission(ATC_PERMISSION_PERSONNEL_VIEW, $userid) )
			{
				echo 'ATTENDEE;ROLE=REQ-PARTICIPANT;'.vcalendaruserstring( $users[$userid]->rank.' '.$users[$userid]->display_name, '', '').$CRLF;
				$description .= '  o '.$users[$userid]->rank.' '.$users[$userid]->display_name.'\\n'.$CRLF;
				$htmldec .= '<li>'.vcalendarsafestring($users[$userid]->rank).' '.vcalendarsafestring($users[$userid]->display_name).'</li>';
			} else {
				echo 'ATTENDEE;ROLE=REQ-PARTICIPANT;'.vcalendaruserstring( $users[$userid]->rank.' '.$users[$userid]->display_name, $users[$userid]->email, $users[$userid]->mobile_phone).$CRLF;
				$description .= '  o '.vcalendarsafestring($users[$userid]->rank).' '.vcalendarsafestring($users[$userid]->display_name).' '.($users[$userid]->mobile_phone?'('.vcalendarsafestring($users[$userid]->mobile_phone).')':'').'\\n'.$CRLF.'    <'.vcalendarsafestring($users[$userid]->email).'>'.'\\n'.$CRLF;
				$htmldesc .= '<li>'.vcalendarsafestring($users[$userid]->rank).' '.vcalendarsafestring($users[$userid]->display_name).' '.($users[$userid]->mobile_phone?'('.vcalendarsafestring($users[$userid]->mobile_phone).')':'').'<br /><a href="mailto='.vcalendarsafestring($users[$userid]->email).'">'.vcalendarsafestring($users[$userid]->email).'</a><ul>';


				$noks = $ATC->get_nok($userid);
				
				foreach($noks as $nok)
				{
					$description .= '     ';
					$htmldesc .= '<li>';
					switch( $nok->relationship )
					{
						case ATC_NOK_TYPE_MOTHER:
							$description .= ' (Mother)';
							$htmldesc .= ' (Mother)';
							break;
						case ATC_NOK_TYPE_FATHER:
							$description .= ' (Father)';
							$htmldesc .= ' (Father)';
							break;
						case ATC_NOK_TYPE_STEPMOTHER:
							$description .= ' (Step-Mother)';
							$htmldesc .= ' (Step-Mother)';
							break;
						case ATC_NOK_TYPE_STEPFATHER:
							$description .= ' (Step-Father)';
							$htmldesc .= ' (Step-Father)';
							break;
						case ATC_NOK_TYPE_SPOUSE:
							$description .= ' (Spouse)';
							$htmldesc .= ' (Spouse)';
							break;
						case ATC_NOK_TYPE_SIBLING:
							$description .= ' (Sibling)';
							$htmldesc .= ' (Sibling)';
							break;
						case ATC_NOK_TYPE_DOMPTNR:
							$description .= ' (Domestic Partner)';
							$htmldesc .= ' (Domestic Partner)';
							break;
						case ATC_NOK_TYPE_GRANDMOTHER:
							$description .= ' (Grandmother)';
							$htmldesc .= ' (Grandmother)';
							break;
						case ATC_NOK_TYPE_GRANDFATHER:
							$description .= ' (Grandfather)';
							$htmldesc .= ' (Grandfather)';
							break;
						default:
							$description .= ' (Other)';
							$htmldesc .= ' (Other)';
							break;
					}
					$description .= ' '.vcalendarsafestring($nok->firstname).' '.vcalendarsafestring($nok->lastname).' <'.vcalendarsafestring($nok->email).'> ('.vcalendarsafestring(str_replace(" ","",$nok->mobile_number)).') ('.vcalendarsafestring(str_replace(" ","",$nok->home_number)).')'.'\\n'.$CRLF;
					$htmldesc .= ' '.vcalendarsafestring($nok->firstname).' '.vcalendarsafestring($nok->lastname).' <a href="'.vcalendarsafestring($nok->email).'">'.vcalendarsafestring($nok->email).'</a> ('.vcalendarsafestring(str_replace(" ","",$nok->mobile_number)).') ('.vcalendarsafestring(str_replace(" ","",$nok->home_number)).')</li>';
				}
				$description .= '';
				$htmldesc .= '</ul></li>';
			}
			$htmldesc .= '</ul>';
			
		}
		if( strlen(trim($description)) )
		{
			$description = 'Cost\: '.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $obj->cost).'\\n'.$CRLF.' Attendees:\\n'.$CRLF.' '.$description;
			$htmldesc = '<html><body><strong>Cost\: '.$ATC->currency_format(ATC_SETTING_FINANCE_MONEYFORMAT, $obj->cost).'<br />Attendees\:</strong> '.$htmldesc.'</body></html>';
			echo 'DESCRIPTION:'.wordwrap($description, 75, "\n  ", true).$CRLF;
			echo 'X-ALT-DESC;FMTTYPE=text/html:'.wordwrap($htmldesc, 75, "\n  ", true).$CRLF;
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
			$string .=':MAILTO:'.$email;
		if(strlen(trim($mobile)))
			$string .=':TEL:+64-'.str_replace(" ","", substr($mobile, 1));
		
		return $string;
	}
	
	exit();
	?>
