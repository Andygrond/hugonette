<?php

namespace Andygrond\Hugonette;

/* HTTP error handling
 * @author Andygrond 2019
**/

class ErrorView
{
	// configuration data
	private static $cfg = [
		'requestBase' => HOME_URI,
		'publishBase' => STATIC_DIR,
		'errorTemplate' => ERROR_PAGE,
	];

	public static function status($code, $par = '')
	{
		bdump('status', 'mess1');
		http_response_code($code);
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			self::errorPage($code);
		} elseif ($par) {
			echo json_encode($par);
		}
		return true;
	}
	
	public static function redirect($code, $to)
	{
		if ($to[0] != '/' && strpos($to, '//') === false) {
			$to = self::$cfg['requestBase'] .$to;
		}
		Log::info($code .' Redirected to: ' .$to);
		header('Location: ' .$to, true, $code);
		return true;
	}
	
	// render error page
	private static function errorPage($code)
	{
		$message = Error::message($code);
		Log::info("Error page: $code");

		$template = self::$cfg['publishBase'] .self::$cfg['errorTemplate'];
		include($template);
	}

}
