<?php

namespace Andygrond\Hugonette;

/* Logger for Hugonette
 * Andrzej Grondziowski 2019
 * Dependency: https://github.com/donatj/PhpUserAgent
**/

class Log
{
	public static $log_name;		// nazwa pliku logu
	public static $screen;			// wyprowadzamy na ekran błędy i ostrzeżenia
	public static $start;				// czas początkowy
	public static $job = '';			// nazwa wykonanego zadania
	public static $collection = '';	// wiadomości do późniejszego zbiorowego zapisania w logu

// start logger
	public static function set($name = 'common')
	{
		self::$start = $_SERVER['REQUEST_TIME_FLOAT'];
		self::$screen = isset($_GET['debug']);
		self::$log_name = LOG_DIR .$name .'.log';
		ini_set("error_log", self::$log_name);
		
		if (!file_exists(self::$log_name)) {
			touch(self::$log_name);		// jesli nie ma pliku, załóż go
			chmod(self::$log_name, 0666);		// nadaj uprawnienia dla cron i usera
		}
	}

// set job name
	public static function job($name)
	{
		$name = ucfirst($name);
		self::$job = "$name: ";
	}

// zwróć sformatowaną wiadomość (do pliku) oraz wyprowadź ją na ekran w uproszczonej formie
	public static function prepare($record, $data)
	{
		if ($data) {
			$record .= ': ' .self::format($data);
		}
		$record = "\t" .self::$job .$record ."\n";

		if (self::$screen) {
			echo str_replace(['\\', '"'], '', nl2br($record));
		}
		return $record;
	}
	
// sformatuj strukturę danych jako string wygodny w czytaniu
	private static function format($data)
	{
		return strtr(json_encode($data, JSON_UNESCAPED_UNICODE), ['"' => '']);
	}

// loguj zdarzenie na ekranie i zbieraj do późniejszego zapisania w pliku
	public static function collect($message, $data = [])
	{
		self::$collection .= self::prepare($message, $data);
	}

// wyprowadź error i zakończ działanie programu
	public static function error($record, $data = [])
	{
		self::close('ERROR: ' .$record, $data);
		exit;
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

// get execution time in miliseconds
	public static function timer()
	{
		$delta = microtime(true) - self::$start;
		$precise = ($delta <0.1)? 1 : 0;
		$delta = round(1000*$delta, $precise);
		return $delta;
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
			$record .= "\n" .self::$collection;
			self::$collection = '';
		}
		file_put_contents(self::$log_name, $record ."\n", FILE_APPEND | LOCK_EX);
	}
	
}
