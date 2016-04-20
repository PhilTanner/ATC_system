<?php
	require_once "atc.class.php";
	
	class ATC_Finance extends ATC
	{
		public function add_payment( $personnel_id, $amount, $reference, $payment_type, $related_to_id )
		{
			if(!self::user_has_permission( ATC_PERMISSION_FINANCE_EDIT ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
				
			$query = "INSERT INTO `payment` (`personnel_id`, `amount`, `reference`, `payment_type`, `related_to_id`, `created_by` ) VALUES ( ".(int)$personnel_id.", ".(float)$amount.", '".self::$mysqli->real_escape_string($reference)."', ".(int)$payment_type.", ".(int)$related_to_id.", ".(int)self::$currentuser." );";
			if ($result = self::$mysqli->query($query))
			{
				self::log_action( 'payment', $query, self::$mysqli->insert_id );
				return true;
			}
			else throw new ATCExceptionDBError(self::$mysqli->error);
		}
		
		function currency_format( $format, $amount )
		{
			$str = '';
			switch($format)
			{
				case ATC_SETTING_MONEYFORMAT_PARENTHESIS:
					if( (float)$amount < 0 )
						$str .= '(';
					$str .= '$ ';
	
					if( (float)$amount < 0 )
						$str .= number_format( (0-(float)$amount), 2, '.', ',' );
					else
						$str .= number_format( (float)$amount, 2, '.', ',' );
					if( (float)$amount < 0 )
						$str .= ')';
					break;
				case ATC_SETTING_MONEYFORMAT_TEXTUAL:
					$str .= '$ ';
					
					if( (float)$amount == 0 )
						$str .= '0.00';
					else if( (float)$amount < 0 )
						$str .= number_format( (0-(float)$amount), 2, '.', ',' );
					else
						$str .= number_format( (float)$amount, 2, '.', ',' ).' cr';
					break;
				case ATC_SETTING_MONEYFORMAT:
				default:
					$str .= '$ ';
					$str .= number_format( (float)$amount, 2, '.', ',' );
					break;
			}
			return $str;
		}
		
		public function get_account_history( $personnel_id, $startdate, $enddate )
		{
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW, $personnel_id) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			if( !strtotime($startdate) )
				throw new ATCExceptionBadData('Invalid startdate');
			if( !strtotime($enddate) )
				throw new ATCExceptionBadData('Invalid enddate');
				
			$query = '
				SELECT
					`personnel`.`personnel_id`,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					`payment`.`reference`,
					CASE 
						WHEN `payment_type` < '.ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE.' 
						THEN (0-`payment`.`amount`) 
						ELSE `payment`.`amount` 
					END AS `amount`, 
					`payment`.`created`,
					`payment`.`payment_type`,
					(
						SELECT
							SUM( 
								CASE 
									WHEN `payment_type` < '.ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE.' 
									THEN (0-`payment`.`amount`) 
									ELSE `payment`.`amount` 
								END 
							) AS `amount`
						FROM 
							`payment`
						WHERE
							`personnel_id` = '.(int)$personnel_id.'
							AND `created` < "'.date('Y-m-d H:i:s', strtotime($startdate) ).'"
					)  AS `opening_balance` 
				FROM 
					`payment`
					INNER JOIN `personnel`
						ON `payment`.`created_by` = `personnel`.`personnel_id`
				WHERE
					`payment`.`personnel_id` = '.(int)$personnel_id.'
					AND `payment`.`created` BETWEEN "'.date('Y-m-d H:i:s', strtotime($startdate) ).'" AND "'.date('Y-m-d H:i:s', strtotime($enddate) ).'"
				ORDER BY 
					`created`;';

			$dues = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$dues[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $dues;
			
		}
		
		public function get_activity_money_outstanding( $personnel_id=null, $activity_id=null  )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW, $personnel_id) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			
			$query = '
				SELECT
					`personnel`.`personnel_id`,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					SUM(`payments_tmp`.`amount_due`) AS `due`,
					SUM(`payments_tmp`.`amount_paid`) AS `paid`,
					( SUM(`payments_tmp`.`amount_due`) + SUM(`payments_tmp`.`amount_paid`) ) AS `remaining`,
					`payments_tmp`.`activity_id`,
					`payments_tmp`.`title`,
					`payments_tmp`.`startdate`
				FROM 
					`personnel`
					LEFT JOIN (
						SELECT 
							`payment`.`personnel_id`,
							CASE WHEN `payment_type` = '.ATC_PAYMENT_TYPE_INVOICE_ACTIVITY_FEE.' THEN `payment`.`amount` ELSE 0 END AS `amount_due`,
							CASE WHEN `payment_type` = '.ATC_PAYMENT_TYPE_RECEIPT_ACTIVITY_FEE.' THEN (0-`payment`.`amount`) ELSE 0 END AS `amount_paid`,
							`activity`.`activity_id` AS `activity_id`,
							`activity`.`title` AS `title`,
							`activity`.`startdate` AS `startdate`
						FROM 
							`payment`
							INNER JOIN `activity`
								ON `payment`.`related_to_id` = `activity`.`activity_id`
						WHERE 
							`payment_type` IN ('.ATC_PAYMENT_TYPE_INVOICE_ACTIVITY_FEE.','.ATC_PAYMENT_TYPE_RECEIPT_ACTIVITY_FEE.')
					) `payments_tmp`
						ON `payments_tmp`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 
					`personnel`.'.(is_null($personnel_id)?'`enabled` = -1':'`personnel_id`='.(int)$personnel_id).'
					AND not (`payments_tmp`.`amount_due` IS NULL AND `payments_tmp`.`amount_paid` IS NULL)
					'.(is_null($activity_id)?'':'AND `payments_tmp`.`activity_id`='.(int)$activity_id).'
				GROUP BY 
					`payments_tmp`.`activity_id`, 
					`personnel`.`personnel_id`
				'.(is_null($personnel_id)&&is_null($activity_id)?'HAVING (SUM( `payments_tmp`.`amount_due` ) + SUM( `payments_tmp`.`amount_paid` )) <> 0':'').'
				ORDER BY 
					`display_name`, 
					`startdate`;';

			$dues = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$dues[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $dues;
		}
		
		public function get_missing_invoices( $personnel_id=null )
		{
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW, $personnel_id) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
				SELECT DISTINCT
					`personnel`.`personnel_id`,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					`term`.`startdate`,
					`term`.`enddate`,
					`term`.`term_id`
				FROM 
					`personnel` 
					INNER JOIN `attendance_register` 
						ON `personnel`.`personnel_id` = `attendance_register`.`personnel_id`
					INNER JOIN `term`
						ON `attendance_register`.`date` between `term`.`startdate` and `term`.`enddate`
						AND `attendance_register`.`presence` IN ( '.ATC_ATTENDANCE_PRESENT.','.ATC_ATTENDANCE_ABSENT_WITHOUT_LEAVE.' )
					LEFT JOIN `payment`
						ON `payment`.`related_to_id` = `term`.`term_id` 
						AND `payment`.`personnel_id` = `personnel`.`personnel_id` 
						AND `payment`.`payment_type` = '.ATC_PAYMENT_TYPE_INVOICE_TERM_FEE.'
				WHERE
					`payment`.`amount` IS NULL
					AND `personnel`.`enabled` = -1
					-- Only cadets pay term fees
					AND `personnel`.`access_rights` IN ( '.ATC_USER_GROUP_CADETS.' );';

			$response = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$response[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $response;
		}
	
		public function get_term_fees_outstanding( $personnel_id=null )
		{
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW, $personnel_id) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			
			$query = '
				SELECT
					`personnel`.`personnel_id`,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					SUM(`payments_tmp`.`amount_due`) AS `due`,
					SUM(`payments_tmp`.`amount_paid`) AS `paid`,
					( SUM(`payments_tmp`.`amount_due`) + SUM(`payments_tmp`.`amount_paid`) ) AS `remaining`,
					-- `payments_tmp`.`term_id`,
					`payments_tmp`.`enddate`,
					`payments_tmp`.`startdate`,
					`payments_tmp`.`term_id`
				FROM 
					`personnel`
					LEFT JOIN (
						SELECT 
							`payment`.`personnel_id`,
							CASE WHEN `payment_type` = '.ATC_PAYMENT_TYPE_INVOICE_TERM_FEE.' THEN `payment`.`amount` ELSE 0 END AS `amount_due`,
							CASE WHEN `payment_type` = '.ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE.' THEN (0-`payment`.`amount`) ELSE 0 END AS `amount_paid`,
							`term`.`startdate`,
							`term`.`enddate`,
							`term`.`term_id`
						FROM 
							`payment`
							INNER JOIN `term`
								ON `payment`.`related_to_id` = `term`.`term_id`
						WHERE 
							`payment_type` IN ('.ATC_PAYMENT_TYPE_INVOICE_TERM_FEE.','.ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE.')
					) `payments_tmp`
						ON `payments_tmp`.`personnel_id` = `personnel`.`personnel_id`
				WHERE 
					`personnel`.`enabled` = -1
					AND not (`payments_tmp`.`amount_due` IS NULL AND `payments_tmp`.`amount_paid` IS NULL)
				GROUP BY 
					`payments_tmp`.`startdate`, 
					`personnel`.`personnel_id`
				HAVING 
					(SUM( `payments_tmp`.`amount_due` ) + SUM( `payments_tmp`.`amount_paid` )) <> 0
				ORDER BY 
					`startdate`,
					`display_name`;';

			$dues = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$dues[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $dues;
		}
	}
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
	{

		/*
		That it is an implementation of the function currency_format for the
		platforms that do not it bear. 
		
		The function accepts to same string of format accepts for the
		original function of the PHP. 
		
		(Sorry. my writing in English is very bad) 
		
		The function is tested using PHP 5.1.4 in Windows XP
		and Apache WebServer.
		*/
		
		function money_format($format, $number)
		{
			$regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';

			if (setlocale(LC_MONETARY, "0") == 'C')
				setlocale(LC_MONETARY, '');
	
			$locale = localeconv();
			preg_match_all($regex, $format, $matches, PREG_SET_ORDER);
			foreach ($matches as $fmatch) 
			{
				$value = floatval($number);
				$flags = array(
					'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ?
					$match[1] : ' ',
					'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
					'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ?
					$match[0] : '+',
					'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
					'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
				);
				$width      = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
				$left       = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
				$right      = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
				$conversion = $fmatch[5];
				
				$positive = true;
				if ($value < 0) 
				{
					$positive = false;
					$value  *= -1;
				}
				$letter = $positive ? 'p' : 'n';
				
				$prefix = $suffix = $cprefix = $csuffix = $signal = '';
				
				$signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
				switch (true) 
				{
					case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
						$prefix = $signal;
						break;
					case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
						$suffix = $signal;
						break;
					case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
						$cprefix = $signal;
						break;
					case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
						$csuffix = $signal;
						break;
					case $flags['usesignal'] == '(':
					case $locale["{$letter}_sign_posn"] == 0:
						$prefix = '(';
						$suffix = ')';
						break;
				}
				if (!$flags['nosimbol']) 
				{
					$currency = $cprefix .
					($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
					$csuffix;
				} else {
					$currency = '';
				}
				$space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';
			
				$value = number_format($value, $right, $locale['mon_decimal_point'],
				$flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
				$value = @explode($locale['mon_decimal_point'], $value);
				
				$n = strlen($prefix) + strlen($currency) + strlen($value[0]);
				if ($left > 0 && $left > $n) 
				{
					$value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
				}
				$value = implode($locale['mon_decimal_point'], $value);
				if ($locale["{$letter}_cs_precedes"]) 
				{
					$value = $prefix . $currency . $space . $value . $suffix;
				} else {
					$value = $prefix . $value . $space . $currency . $suffix;
				}
				if ($width > 0) {
					$value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);
				}
				
				$format = str_replace($fmatch[0], $value, $format);
			}
			return $format;
		} 
		
	}
	
?>