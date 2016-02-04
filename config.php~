<?php
	$mtime = explode(" ",microtime()); 
	$time = $mtime[1] + $mtime[0];
	$timers = array(array($time, 0, 'Init'));

	function recordTimestamp( $description )
	{
		global $timers;
		/*
		$mtime = explode(" ",microtime()); 
		$time = $mtime[1] + $mtime[0];
		
		$lasttimestamp = $timers[count($timers)-1][0];
		$timers[] = array($time, ($time-$lasttimestamp), $description, debug_backtrace());
		*/
	}
	
	define( 'DB_USER', 'root' );
	define( 'DB_PSWD', '' );
	define( 'DB_HOST', 'localhost' );
	define( 'DB_NAME', 'pkbasc' );
	
	define( 'MONEYFORMAT', '%(#2.2n' );
	
	define( 'BOOKED',  0 );
	define( 'ABSENT',  1 );
	define( 'REQUEST', 2 );
	
	define( 'MONEYFORMAT_NORMAL',      0 );
	define( 'MONEYFORMAT_PARENTHESIS', 1 );
	define( 'MONEYFORMAT_TEXTUAL',     2 );
	
	setlocale(LC_ALL, 'en_NZ.UTF-8');

	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
	{
		// http://msdn.microsoft.com/en-us/library/cdax410z%28v=vs.90%29.aspx
		setlocale(LC_ALL, 'enz');

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
		define('CMD_BACKUP', 'c:\xampp\mysql\bin\mysqldump -ifh '.DB_HOST.' --dump-date -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' >'.dirname($_SERVER['SCRIPT_FILENAME']).'/backups/'.DB_NAME.'-');
		define('CMD_BACKUP_AUTOMATED', 'c:\xampp\mysql\bin\mysqldump -ifh '.DB_HOST.' --dump-date -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' >'.dirname($_SERVER['SCRIPT_FILENAME']).'/automatedBackups/'.DB_NAME.'-');
		define('CMD_RESTORE', 'c:\xampp\mysql\bin\mysql -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' <'.dirname($_SERVER['SCRIPT_FILENAME']).'/backups/');

	} else {
		define('CMD_BACKUP', 'mysqldump -ifh '.DB_HOST.' --dump-date -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' >'.dirname($_SERVER['SCRIPT_FILENAME']).'/backups/'.DB_NAME.'-');
		define('CMD_BACKUP_AUTOMATED', 'mysqldump -ifh '.DB_HOST.' --dump-date -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' >'.dirname($_SERVER['SCRIPT_FILENAME']).'/automatedBackups/'.DB_NAME.'-');
		define('CMD_RESTORE', 'mysql -u '.DB_USER.' '.(strlen(DB_PSWD)?'-p "'.DB_PSWD.'" ':'').DB_NAME.' <'.dirname($_SERVER['SCRIPT_FILENAME']).'/backups/');
	}
	
	require_once 'pkbasc.class.php';
	$PKBASC = new PKBASC();

	/* Function to work out what students are due to attend during a week, which have bookings, which have known absences and which have requested one-off attendance */
	function studentBookings( $weekcommencing )
	{
		global $PKBASC;
		return $PKBASC->getStudentBookings( $weekcommencing );
	}
	
	function currency_format( $format, $amount )
	{
		global $PKBASC;
		return $PKBASC->currency_format( $format, $amount );
	}
	
?>
