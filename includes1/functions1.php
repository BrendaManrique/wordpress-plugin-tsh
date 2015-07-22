<?php
	/*
     * Function to sum an array of times into a total
	 *
	 * @param 			$times 		The array of Hours
     * @return 			string		The Total (format: 00:00:00)
     */
	function sumHours($times) {
		$seconds = 0;
		foreach ($times as $time) {
			list($hour,$minute,$second) = explode(':', $time);
			$seconds += $hour*3600;
			$seconds += $minute*60;
			$seconds += $second;
		}

		$hours = floor($seconds/3600);
		$seconds -= $hours*3600;
		$minutes = floor($seconds/60);
		$seconds -= $minutes*60;
		return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
	}

	/*
     * Functions to get the Week Number from a date
	 * Returns the Week Number starting from Sunday
     */
	function getIsoWeeksInYear($year) {
		$date = new DateTime;
		$date->setISODate($year, 53);
		return ($date->format("W") === "53" ? 53 : 52);
	}

	function getWeekNo($date) {
		$week = date('W',strtotime($date));
		$day = date('N',strtotime($date));
		$max_weeks = getIsoWeeksInYear(date('Y',strtotime($date)));

		if($day == 7 && $week != $max_weeks) {
			return ++$week;
		} else if ($day == 7) {
			return '01';
		} else {
			return $week;
		}
	}
	
/*
     * Function to convert a number into readable Currency
     *
     * @param string $n   			The number
     * @param string $n_decimals	The decimal position
     * @return string           	The formatted Currency Amount
	 *
	 * Returns string type, rounded number - same as php number_format()):
	 *
	 * Examples:
	 *		format_amount(54.377, 2) 	returns 54.38
	 *		format_amount(54.004, 2) 	returns 54.00
	 *		format_amount(54.377, 3) 	returns 54.377
	 *		format_amount(54.00007, 3) 	returns 54.00
     */
	function format_amount($n, $n_decimals) {
        return ((floor($n) == round($n, $n_decimals)) ? number_format($n).'.00' : number_format($n, $n_decimals));
    }

    /*
     * Function to show an Alert type Message Box
     *
     * @param string $message   The Alert Message
     * @param string $icon      The Font Awesome Icon
     * @param string $type      The CSS style to apply
     * @return string           The Alert Box
     */
    function alertBox($message, $icon = "", $type = "") {
        return "<div class=\"alertMsg $type\"><span>$icon</span> $message <a class=\"alert-close\" href=\"#\">x</a></div>";
    }

    /*
     * Function to ellipse-ify text to a specific length
     *
     * @param string $text      The text to be ellipsified
     * @param int    $max       The maximum number of characters (to the word) that should be allowed
     * @param string $append    The text to append to $text
     * @return string           The shortened text
     */
    function ellipsis($text, $max = '', $append = '&hellip;') {
        if (strlen($text) <= $max) return $text;

        $replacements = array(
            '|<br /><br />|' => ' ',
            '|&nbsp;|' => ' ',
            '|&rsquo;|' => '\'',
            '|&lsquo;|' => '\'',
            '|&ldquo;|' => '"',
            '|&rdquo;|' => '"',
        );

        $patterns = array_keys($replacements);
        $replacements = array_values($replacements);

        // Convert double newlines to spaces.
        $text = preg_replace($patterns, $replacements, $text);
        // Remove any HTML.  We only want text.
        $text = strip_tags($text);
        $out = substr($text, 0, $max);
        if (strpos($text, ' ') === false) return $out.$append;
        return preg_replace('/(\W)&(\W)/', '$1&amp;$2', (preg_replace('/\W+$/', ' ', preg_replace('/\w+$/', '', $out)))).$append;
    }

    /*
     * Function to Encrypt sensitive data for storing in the database
     *
     * @param string	$value		The text to be encrypted
	 * @param 			$encodeKey	The Key to use in the encryption
     * @return						The encrypted text
     */
	function encryptIt($value) {
		// The encodeKey MUST match the decodeKey
		$encodeKey = 'dS+D.f^Li1KUqJ4tgX14`Jy,A9ejk?uwnXaNSD@fQ+dCZIF!';
		$encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encodeKey), $value, MCRYPT_MODE_CBC, md5(md5($encodeKey))));
		return($encoded);
	}

    /*
     * Function to decrypt sensitive data from the database for displaying
     *
     * @param string	$value		The text to be decrypted
	 * @param 			$decodeKey	The Key to use for decryption
     * @return						The decrypted text
     */
	function decryptIt($value) {
		// The decodeKey MUST match the encodeKey
		$decodeKey = 'dS+D.f^Li1KUqJ4tgX14`Jy,A9ejk?uwnXaNSD@fQ+dCZIF!';
		$decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decodeKey), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($decodeKey))), "\0");
		return($decoded);
	}

	/*
     * Function to strip slashes for displaying database content
     *
     * @param string	$value		The string to be stripped
     * @return						The stripped text
     */
	function clean($value) {
		$str = str_replace('\\', '', $value);
		return $str;
	}
	
	/*
     * Get all of the active Employee emails
     */
	$empSql = "SELECT empEmail FROM employees WHERE isActive = 1";
	$empResult = mysqli_query($mysqli, $empSql) or die('-98' . mysqli_error());

	// Set each email into a CSV
	$empEmailList = array();
	while ($e = mysqli_fetch_assoc($empResult)) {
		$empEmailList[] = $e['empEmail'];
	}
	$allEmpEmails = implode(',',$empEmailList);
?>