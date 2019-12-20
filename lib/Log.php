<?php

namespace Andygrond\Hugonette;

/* Simple logger for Tracy debugger
 * @author Andygrond 2019
 * Optional dependency: https://github.com/donatj/PhpUserAgent
**/

// todo: error levels and mailing: https://doc.nette.org/en/2.1/debugging
// todo: set warnings in model
// todo: send job $elapsed time somewhere

use Tracy\Debugger;

class Log
{
	private static $log_name;		// nazwa pliku logu
	private static $job = '';			// nazwa wykonanego zadania

	private static $collection = [];	// wiadomości do późniejszego zbiorowego zapisania w logu
	private static $jobStack = [];	// job names stack

	// configuration data
	private static $cfg = [
		'logDir' => __DIR__ . '/../log/',
	];

// set log file name and Tracy debugger mode
// default log file name = log/error.log
// default mode = IP detection
	public static function set($name, $mode = '')
	{
		$log_name = $this->cfg['logDir'] .$name .'.log';
//		ini_set("error_log", $log_name);
		
		if ($mode) {
			$log_mode = strncasecmp($mode, 'dev', 3)? Debugger::PRODUCTION : Debugger::DEVELOPMENT;
		} else {
			$log_mode = Debugger::DETECT;
		}
		
		if (!file_exists($log_name)) {
			touch($log_name);		// jesli nie ma pliku, załóż go
			chmod($log_name, 0666);		// nadaj uprawnienia dla cron i usera
		}

		Debugger::enable($log_mode, $log_name);
	}

// set job name
	public static function job($name)
	{
		$name = ucfirst($name);
		self::$jobStack[] = $name;
		self::$job = "$name: ";
		Debugger::timer($name);
	}

// reset old job name
	public static function jobDone()
	{
		$name = array_pop(self::$jobStack);
		$elapsed = Debugger::timer($name);
		// todo: send $elapsed somewhere

		$name = end(self::$jobStack);
		self::$job = "$name: ";
	}

// zarejestruj wiadomość i zbieraj do późniejszego zapisania w pliku
	public static function collect($message, $data = [])
	{
		self::$collection[] = [
			'message' => $message,
			'data' => $data,
		];
		
	}

// wyprowadź error i zakończ działanie programu
	public static function error($record, $data = [])
	{
		self::collect('ERROR: ' .$record, $data);
	}

// wyprowadź warning
	public static function warning($record, $data = [])
	{
		self::collect('WARNING: ' .$record, $data);
	}

// wyprowadź info
	public static function info($record, $data = [])
	{
		self::collect($record, $data);
	}

	public static function close($info = '', $data = [])
	{
		$record = date('Y-m-d H:i:s ') .$_SERVER['REMOTE_ADDR'] .' [' .self::timer() .' ms] ';
		if (is_callable('parse_user_agent')) {
			$agent = parse_user_agent();
			$record .= $agent['browser'] .' ' .strstr($agent['version'], '.', true) .' on ' .$agent['platform'] .' ';
		}
		$record .= $_SERVER['REQUEST_METHOD'] .' ' .$_SERVER['REQUEST_URI'];
		
		if ($info) {
			self::collect($info, $data);
		}
		if (self::$collection) {
			foreach (self::$collection as $item) {
				$record .= "\n" .$item['message'] .' | ' .json_encode($item['data']);
			}
			self::$collection = '';
		}
		Debugger::log($record ."\n", FILE_APPEND | LOCK_EX);
	}
	
}
