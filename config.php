<?php
	$mtime = explode(" ",microtime()); 
	$time = $mtime[1] + $mtime[0];
	$timers = array(array($time, 0, 'Init'));

	define( 'DB_USER', 'atc' );
	define( 'DB_PSWD', 'ZIERIESs5ESa' );
	define( 'DB_HOST', 'localhost' );
	define( 'DB_NAME', 'atc' );
	
	define( 'MONEYFORMAT', '%(#2.2n' );
	
	setlocale(LC_ALL, 'en_NZ.UTF-8');
	date_default_timezone_set('Pacific/Auckland');

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
	
	/*
	 * Password Hashing With PBKDF2 (http://crackstation.net/hashing-security.htm).
	 * Copyright (c) 2013, Taylor Hornby
	 * All rights reserved.
	 *
	 * Redistribution and use in source and binary forms, with or without 
	 * modification, are permitted provided that the following conditions are met:
	 *
	 * 1. Redistributions of source code must retain the above copyright notice, 
	 * this list of conditions and the following disclaimer.
	 *
	 * 2. Redistributions in binary form must reproduce the above copyright notice,
	 * this list of conditions and the following disclaimer in the documentation 
	 * and/or other materials provided with the distribution.
	 *
	 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
	 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
	 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE 
	 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
	 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
	 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
	 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
	 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
	 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	 * POSSIBILITY OF SUCH DAMAGE.
	 */
	
	// These constants may be changed without breaking existing hashes.
	define("PBKDF2_HASH_ALGORITHM", "sha256");
	define("PBKDF2_ITERATIONS", 1000);
	define("PBKDF2_SALT_BYTE_SIZE", 24);
	define("PBKDF2_HASH_BYTE_SIZE", 24);
	
	define("HASH_SECTIONS", 4);
	define("HASH_ALGORITHM_INDEX", 0);
	define("HASH_ITERATION_INDEX", 1);
	define("HASH_SALT_INDEX", 2);
	define("HASH_PBKDF2_INDEX", 3);
	
	function create_hash($password)
	{
		// format: algorithm:iterations:salt:hash
		$salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
		return PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" .  $salt . ":" .
			base64_encode(pbkdf2(
				PBKDF2_HASH_ALGORITHM,
				$password,
				$salt,
				PBKDF2_ITERATIONS,
				PBKDF2_HASH_BYTE_SIZE,
				true
			));
	}
	
	function validate_password($password, $correct_hash)
	{
		$params = explode(":", $correct_hash);
		if(count($params) < HASH_SECTIONS)
		   return false;
		$pbkdf2 = base64_decode($params[HASH_PBKDF2_INDEX]);
		return slow_equals(
			$pbkdf2,
			pbkdf2(
				$params[HASH_ALGORITHM_INDEX],
				$password,
				$params[HASH_SALT_INDEX],
				(int)$params[HASH_ITERATION_INDEX],
				strlen($pbkdf2),
				true
			)
		);
	}
	
	// Compares two strings $a and $b in length-constant time.
	function slow_equals($a, $b)
	{
		$diff = strlen($a) ^ strlen($b);
		for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
		{
			$diff |= ord($a[$i]) ^ ord($b[$i]);
		}
		return $diff === 0;
	}
	
	/*
	 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
	 * $algorithm - The hash algorithm to use. Recommended: SHA256
	 * $password - The password.
	 * $salt - A salt that is unique to the password.
	 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
	 * $key_length - The length of the derived key in bytes.
	 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
	 * Returns: A $key_length-byte key derived from the password and salt.
	 *
	 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
	 *
	 * This implementation of PBKDF2 was originally created by https://defuse.ca
	 * With improvements by http://www.variations-of-shadow.com
	 */
	function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
	{
		$algorithm = strtolower($algorithm);
		if(!in_array($algorithm, hash_algos(), true))
			trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
		if($count <= 0 || $key_length <= 0)
			trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
	
		if (function_exists("hash_pbkdf2")) {
			// The output length is in NIBBLES (4-bits) if $raw_output is false!
			if (!$raw_output) {
				$key_length = $key_length * 2;
			}
			return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
		}
	
		$hash_length = strlen(hash($algorithm, "", true));
		$block_count = ceil($key_length / $hash_length);
	
		$output = "";
		for($i = 1; $i <= $block_count; $i++) {
			// $i encoded as 4 bytes, big endian.
			$last = $salt . pack("N", $i);
			// first iteration
			$last = $xorsum = hash_hmac($algorithm, $last, $password, true);
			// perform the other $count - 1 iterations
			for ($j = 1; $j < $count; $j++) {
				$xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
			}
			$output .= $xorsum;
		}
	
		if($raw_output)
			return substr($output, 0, $key_length);
		else
			return bin2hex(substr($output, 0, $key_length));
	}	
?>
