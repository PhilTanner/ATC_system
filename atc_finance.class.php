<?php
	require_once "atc.class.php";
	
	define( 'ATC_SETTING_MONEYFORMAT_PARENTHESIS', 0 );
	define( 'ATC_SETTING_MONEYFORMAT_TEXTUAL', 1 );
	
	define( 'ATC_PAYMENT_TYPE_INVOICE_TERM_FEE', 			0 );
	define( 'ATC_PAYMENT_TYPE_INVOICE_ACTIVITY_FEE', 		1 );
	define( 'ATC_PAYMENT_TYPE_INVOICE_OUTSTANDING_MONEY',	2 );
	define( 'ATC_PAYMENT_TYPE_INVOICE_UNIFORM_DEPOSIT',		3 );
	define( 'ATC_PAYMENT_TYPE_INVOICE_FUNDRAISING',			4 );
	define( 'ATC_PAYMENT_TYPE_INVOICE_MISCELLANEOUS',		19 );
	
	define( 'ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE', 			20 );
	define( 'ATC_PAYMENT_TYPE_RECEIPT_ACTIVITY_FEE', 		21 );
	define( 'ATC_PAYMENT_TYPE_RECEIPT_OUTSTANDING_MONEY', 	22 );
	define( 'ATC_PAYMENT_TYPE_RECEIPT_UNIFORM_DEPOSIT', 		23 );
	define( 'ATC_PAYMENT_TYPE_RECEIPT_FUNDRAISING',			24 );
	define( 'ATC_PAYMENT_TYPE_RECEIPT_MISCELLANEOUS',		39 );
	
	
	class ATC_Finance extends ATC
	{
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
		
		public function get_activity_money_outstanding(  )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
						
			$query = '
				SELECT	`activity_register`.*,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					`personnel`.`mobile_phone`,
					`activity`.`cost`,
					`activity`.`title`,
					`activity`.`startdate`,
					`activity`.`enddate`
				FROM 	`activity_register`
					INNER JOIN `personnel`
						ON `activity_register`.`personnel_id` = `personnel`.`personnel_id`
					INNER JOIN `activity`
						ON `activity_register`.`activity_id` = `activity`.`activity_id`
				WHERE 	`activity_register`.`amount_paid` < `activity`.`cost`
					-- Only cadets pay fees
					AND `personnel`.`access_rights` IN ( '.ATC_USER_GROUP_CADETS.' )
				ORDER BY `activity`.`startdate`, `activity`.`title`, `personnel`.`lastname`, `personnel`.`firstname`;';

			$dues = array();
			if ($result = self::$mysqli->query($query))
				while ( $obj = $result->fetch_object() )
					$dues[] = $obj;
			else
				throw new ATCExceptionDBError(self::$mysqli->error);

			return $dues;
		}
	
		public function get_term_fees_outstanding( $personnel_id=null )
		{
			if(!self::user_has_permission( ATC_PERMISSION_ACTIVITIES_VIEW ))
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
			if( !self::user_has_permission(ATC_PERMISSION_FINANCE_VIEW) )
			    throw new ATCExceptionInsufficientPermissions("Insufficient rights to view this page");
						
			$query = '
				SELECT `personnel`.`personnel_id`,
					'.ATC_SETTING_DISPLAY_NAME.' AS `display_name`,
					'.ATC_SETTING_DISPLAY_RANK_SHORTNAME.' AS `rank`,
					`debts`.`amount_due`,
					`credits`.`amount_paid`
				FROM	`personnel`
					LEFT JOIN ( 
						SELECT `payment`.`personnel_id`,
							SUM(`amount`) AS `amount_due`
						FROM	`payment`
							-- Left join because outstading monies do not have terms assoc with themm, but they still need to be counted
							LEFT JOIN `term` 
								ON `payment`.`related_to_id` = `term`.`term_id`
						WHERE `payment_type` IN ('.ATC_PAYMENT_TYPE_INVOICE_TERM_FEE.','.ATC_PAYMENT_TYPE_INVOICE_OUTSTANDING_MONEY.')
						GROUP BY `payment`.`personnel_id`
					) `debts`
						ON `debts`.`personnel_id` = `personnel`.`personnel_id`
					LEFT JOIN ( 
						SELECT `payment`.`personnel_id`,
							SUM(`amount`) AS `amount_paid`
						FROM	`payment`
							-- Left join because outstading monies do not have terms assoc with themm, but they still need to be counted
							LEFT JOIN `term` 
								ON `payment`.`related_to_id` = `term`.`term_id`
						WHERE `payment_type` IN ('.ATC_PAYMENT_TYPE_RECEIPT_TERM_FEE.','.ATC_PAYMENT_TYPE_RECEIPT_OUTSTANDING_MONEY.')
						GROUP BY `payment`.`personnel_id`
					) `credits`
						ON `credits`.`personnel_id` = `personnel`.`personnel_id`
					WHERE `personnel`.`enabled` = -1
						'.(is_null($personnel_id)?'':'AND `personnel`.`personnel_id` = '.(int)$personnel_id).'
					HAVING (`debts`.`amount_due` - `credits`.`amount_paid`) <> 0';

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