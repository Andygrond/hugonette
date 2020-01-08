<?php

namespace Andygrond\Hugonette;

/* Simple logger for Tracy debugger
 * @author Andygrond 2019
 * Optional dependency: https://github.com/donatj/PhpUserAgent
**/

// todo: error levels and mailing: https://doc.nette.org/en/2.1/debugging
// todo: send job $elapsed time somewhere

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Log
{
	public static $viewErrors = [];	// messages collected to be passed to view

	private static $collection = [];	// messages waiting for output to file
	private static $jobStack = [];		// job names stack
	private static $wasSet = false;	// log is active

	// configuration data
	private static $cfg = [
		'logDir' => LIB_DIR .'log',
		'email' => ADMIN_EMAIL,
		'allowedTypes' => [ 'error', 'warning', 'view', 'info' ],
	];

// set log file name and Tracy debugger mode
// default log file name = log/error.log
	public static function set($mode)
	{
		$log_mode = strncasecmp($mode, 'dev', 3)? Debugger::PRODUCTION : Debugger::DEVELOPMENT;
		self::$wasSet = true;
		Debugger::enable($log_mode, self::$cfg['logDir']);
		
		if (self::$cfg['email']) {
			Debugger::$email = $cfg['email'];
		}

//		Debugger::$strictMode = true;	// log all error types
//		Debugger::$logSeverity = E_NOTICE | E_WARNING | E_USER_WARNING;	// log html screens
//		Debugger::dispatch();		// do it after session reloading
	}

// output the message 
// $args = [record, data]
	public static function __callStatic($type, $args)
	{
		if (in_array($type, self::$cfg['allowedTypes'])) {
			if ($type == 'view') {
				self::$viewErrors[] = $args[0];
			}

			$message = strtoupper($type) .': ';
			if (self::$jobStack) {
				$message .= end(self::$jobStack) .': ';
			}

			self::$collection[] = [
				'message' => $message .$args[0],
				'data' => isset($args[1])? json_encode($args[1]) : '',
			];
			
		} else {
			trigger_error("Log message type: $type not allowed", E_USER_WARNING);
		}
	}

// enable Tracy output debugger
	public static function output()
	{
		OutputDebugger::enable();
	}

// set job name
	public static function job($name)
	{
		$name = ucfirst($name);
		self::$jobStack[] = $name;
		Debugger::timer($name);
	}

// reset old job name
	public static function jobDone()
	{
		$name = array_pop(self::$jobStack);
		$elapsed = Debugger::timer($name);
		// todo: send $elapsed somewhere
	}

// put all collected messages to log file
	public static function close()
	{
		if (!self::$wasSet) {
			return;
		}
		
		$record = '[' .Debugger::timer() .' ms] ' .$_SERVER['REMOTE_ADDR'];
		if (is_callable('parse_user_agent')) {
			$agent = parse_user_agent();
			$record .= ' ' .$agent['browser'] .' ' .strstr($agent['version'], '.', true) .' on ' .$agent['platform'];
		}
		$record .= ' ' .$_SERVER['REQUEST_METHOD'];
		
		if (self::$collection) {
			foreach (self::$collection as $item) {
				$record .= "\n" .$item['message'] .' | ' .$item['data'];
			}
			self::$collection = '';
		}
		Debugger::log($record);
	}
	
}
