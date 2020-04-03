<?php

namespace Andygrond\Hugonette;

/* Log Facade for Hugonette
 * Uses PSR-3 log levels + level 'view' which collects messages for user awareness
 * Channel 'tracy' and 'ajax' utilizes Tracy debugger
 * For channel 'ajax' use Chrome with FireLogger extension
 *
 * @author Andygrond 2019
 * Dependency: https://github.com/nette/tracy
 * Dependency: https://github.com/donatj/PhpUserAgent
 * todo: tests of mailing, output debugger, ajax channel
**/

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Log
{
  private const LEVELS = [   // message level hierachy
    'debug'     => 1,
    'info'      => 2,
    'view'      => 3,
    'notice'    => 4,
    'warning'   => 5,
    'error'     => 6,
    'critical'  => 7,
    'alert'     => 8,
    'emergency' => 9,
  ];

  private static $collection = []; // messages waiting for output
  private static $jobStack = [];   // job names stack

  private static $channel;         // active output channel
  private static $logPath;         // path to log files
  private static $logFile = '';    // path to log filename for file channel
  private static $minLevel;        // lowest level of logged messages

  public static $viewErrors = [];  // messages collected to be passed to view
  public static $durations = [];   // elapsed job times
  public static $isActive = false; // log is set and active
  public static $debug = false;    // debug mode flag

  /* log initialization
    @ $path - /path/to/log/filename.log or /path/to/log/folder/
    extension .log is obligatory for files
    omitted filename can be set later, or native tracy log will be used
    @ $channel - set main log channel
    @ $mode = debugger mode ['dev'|'prod']
  */
  public static function set(string $path, string $channel, string $mode = 'prod')
  {
    if (self::$isActive) {
      throw new \BadMethodCallException("Log can not be set twice");
    }
    self::$isActive = true;
    self::$debug = !strncasecmp($mode, 'dev', 3);
    self::$minLevel = self::$debug? 1 : 2;

    if (strrchr($path, '.') == '.log') {
      $pos = strrpos($path, '/')+1;
      self::$logPath = rtrim(substr($path, 0, $pos), '/') .'/';
      self::$logFile = $path;
    } else {
      self::$logPath = rtrim($path, '/') .'/';
    }

    self::channel($channel);
  }

  // set lowest level of registered messages
  public static function level(string $level)
  {
    if (!self::$minLevel = @self::LEVELS[$level]) {
      throw new \UnexpectedValueException("Log level: $level is not valid. Use one of [" .implode('|', array_keys(self::LEVELS)) .']');
    }
  }

  // set or change log file name, relative to path used in "set" method
  public static function filename(string $filename)
  {
    self::$logFile = self::$logPath .$filename;
  }

  // for mailing feature in 'tracy' or 'ajax' channel (not tested)
  public static function email(string $email)
  {
    Debugger::$email = $email;
  }

  // enable Tracy output debugger
  public static function output()
  {
    OutputDebugger::enable();
  }

  // set log channel
  // $channel: tracy, ajax, or plain - Tracy will not be used in "plain" channel
  public static function channel(string $channel)
  {
    if (!self::$isActive) {
      throw new \BadMethodCallException("Log must be set to be able to set channel");
    }
    self::$channel = $channel;

    switch($channel) {
      case 'tracy':
        $logMode = self::$debug? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
        Debugger::enable($logMode, self::$logPath);
        // Debugger::$strictMode = true;  // log all error types
        // Debugger::$logSeverity = E_NOTICE | E_WARNING | E_USER_WARNING;  // log html screens
        // Debugger::dispatch();  // do it after session reloading
        break;
      case 'file':
      case 'ajax':
        break;
      default:
        throw new \UnexpectedValueException("Log channel: $channel is not valid. Use one of [" .implode('|', array_keys(self::CHANNELS)) .']');
    }
  }

  // output the message
  // $args = [record, data]
  public static function __callStatic(string $level, array $args)
  {
    if (!$levelNo = @self::LEVELS[$level]) {
      throw new \BadMethodCallException('Log method not found: ' .$level);
    }
    if (!self::$isActive) {
      throw new \BadMethodCallException('Log must be set to be used');
    }

    [$record, $data] = array_pad($args, 2, '');
    if ($level == 'view') {  // collect view errors
      self::$viewErrors[] = $record;
    }

    if ($levelNo >= self::$minLevel) {  // message filtering
      $message = strtoupper($level) .': ';
      if (self::$jobStack) {
        $message .= end(self::$jobStack) .': ';
      }
      self::$collection[] = [
        'message' => $message .$record,
        'data' => isset($args[1])? json_encode($data, JSON_UNESCAPED_UNICODE) : '',
      ];
    }
  }

  // put all collected messages to log file
  public static function close()
  {
    if (!self::$isActive) {
      return;
    }
    self::$isActive = false;
    $record = self::renderRecord();

    if (self::$logFile) {
      file_put_contents(self::$logFile, date('Y-m-d H:i:s.u ') .$record ."\n", FILE_APPEND | LOCK_EX);
    } else {
      Debugger::log($record);
    }

    if (self::$channel == 'ajax') {
      Debugger::fireLog($record);
    }
  }

  private static function renderRecord()
  {
    if (php_sapi_name() == "cli") {
      $record = 'Command Line';
    } elseif (is_callable('parse_user_agent') && isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = parse_user_agent();
      $record = $agent['browser'] .' ' .strstr($agent['version'], '.', true); // .' on ' .$agent['platform'];
    } else {
      $record = '';
    }

    if (self::$collection) {
      foreach (self::$collection as $item) {
        $record .= "\n" .$item['message'] .' | ' .$item['data'];
      }
      self::$collection = '';
    }

    return $_SERVER['REMOTE_ADDR'] .' ' .self::timesTxt() .' ' .$_SERVER['REQUEST_METHOD'] .' ' .$record;
  }

  // set job name
  public static function job(string $name)
  {
    self::$jobStack[] = $name;
    if (@self::$durations[$name]['start'])
      throw new \InvalidArgumentException("Job $name double start.");

    self::$durations[$name]['start'] = microtime(true);
    if (!@self::$durations[$name]['duration']) {
      self::$durations[$name]['duration'] = 0;
    }
  }

  // quit current job
  // reset old job name and save job duration
  public static function done(string $name)
  {
    $lastName = array_pop(self::$jobStack);
    if ($name != $lastName || !isset(self::$durations[$name]))
      throw new \InvalidArgumentException("Job $name interlaces with another. Nesting is allowed only.");
    if (!@self::$durations[$name]['start'])
      throw new \InvalidArgumentException("Job $name done but not started.");

    self::$durations[$name]['duration'] += microtime(true) - self::$durations[$name]['start'];
    self::$durations[$name]['start'] = 0;
  }

  // get array of all durations
  public static function times(): array
  {
    $times = [];
    $appTime = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
    $times[] = self::easyTime($appTime);

    if (self::$durations) {
      foreach (self::$durations as $name => $frame) {
        $times[] = $name .': ' .self::easyTime($frame['duration']);
      }
    }
    return $times;
  }

  // get time diration in user friendly format
  // argument in milliseconds
  	public static function easyTime(int $duration): string
  	{
  		if ($duration > 90000) {
  			$info =  round($duration/60000, 1) .' min';
  		} elseif ($duration > 800) {
  			$lo = ($duration > 10000)? 1 : ($duration > 1000)? 2 : 3;
  			$info = round($duration/1000, $lo) .' s';
  		} else {
  			$info = round($duration) .' ms';
  		}
  		return $info;
  	}


}
