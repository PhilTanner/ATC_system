<?php
	require_once "atc.class.php";
	$ATC = new ATC();
	
	$id   = ( isset($_GET['id'])?$_GET['id']:null );
	$what = ( isset($_GET['what']) && trim(strtolower($_GET['what'])) == 'personal'?'personal':'nok' );
	$how  = ( isset($_GET['how'])  && trim(strtolower($_GET['how']))  == 'email'?'email':'sms' );
	
	$returnvalue = array();
	
	if( is_array( $id ) ) 
	{
		foreach( $id as $personnel )
		{
			if( $ATC->user_has_permission( ATC_PERMISSION_PERSONNEL_VIEW, $personnel ) )
			{
				switch ( strtolower(trim($what)) )
				{
					case 'personal':
						$details = $ATC->get_personnel($personnel, 'ASC', null);
						
						if( $details && isset($details->display_name) )
						{
							switch ( strtolower(trim($how)) )
							{
								case 'sms':
									if( $details->mobile_phone )
										$returnvalue[] = $details->mobile_phone;
									break;
								case 'email':
									$returnvalue[] = '"'.$details->rank.' '.$details->display_name.'" <'.$details->email.'>';
									break;
								default:
									throw new ATCExceptionBadData('Unknown "how" URL variable.');
							}
						}
						break;
						
					case 'nok':
						$details = $ATC->get_nok($personnel);
						
						if( $details && count($details) )
						{
							foreach( $details as $nok )
							{
								switch ( strtolower(trim($how)) )
								{
									case 'sms':
										if( $nok->mobile_number )
											$returnvalue[] = $nok->mobile_number;
										break;
									case 'email':
										$returnvalue[] = '"'.$nok->firstname.' '.$nok->lastname.'" <'.$nok->email.'>';
										break;
									default:
										throw new ATCExceptionBadData('Unknown "how" URL variable.');
								}
							}
						}
						break;
						
					default:
						throw new ATCExceptionBadData('Unknown "what" URL variable.');
				}
			}
		}
	}
	
	header('Content-Type: application/json; charset=utf-8');
	// Dedupe our list in case someone appears more than once (i.e. NOK for cadet, NOK for SNCO & USC member)
	echo json_encode(array_unique($returnvalue));
	
?>