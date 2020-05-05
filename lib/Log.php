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
    @ $path = /path/to/log/filename.log or /path/to/log/folder/
      extension .log is obligatory for files
      omitted filename can be set later, or native tracy log will be used
    @ $channel - set main log channel
    @ $mode = debugger mode ['dev'|'prod']
  */
  public static function set(string $path, string $channel, string $mode = 'prod')
  {
    // calculate init time duration
    self::$durations['init'] = [
      'start' => $_SERVER["REQUEST_TIME_FLOAT"],
      'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
    ];

    // initialize variables
    if (self::$isActive) {
      throw new \BadMethodCallException("Log cannot be set twice");
    }
    self::$isActive = true;
    self::$debug = !strncasecmp($mode, 'dev', 3);
    self::$minLevel = self::$debug? 1 : 2;

    // set log file
    if (strrchr($path, '.') == '.log') {
      $pos = strrpos($path, '/')+1;
      self::$logPath = rtrim(substr($path, 0, $pos), '/') .'/';
      self::$logFile = $path;
      ini_set('error_log', $path);
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
  public static function outputDebugger()
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
      case 'ajax':
        $logMode = self::$debug? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
        Debugger::enable($logMode, self::$logPath);
        // Debugger::$strictMode = true;  // log all error types
        // Debugger::$logSeverity = E_NOTICE | E_WARNING | E_USER_WARNING;  // log html screens
        // Debugger::dispatch();  // do it after session reloading
        break;
      case 'plain':
        if (self::$debug) {
          ini_set('display_errors', true);
        }
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
    self::flush();
  }

  // forced write to file
  public static function flush()
  {
    $request = self::renderRequest();
    $message = self::renderMessage();

    if (self::$logFile) {
      $date = \DateTime::createFromFormat('U,u', (string) $_SERVER["REQUEST_TIME_FLOAT"]);
      $request .= ' ' .$_SERVER['REQUEST_URI'];
      file_put_contents(self::$logFile, $date->format('Y-m-d H:i:s.v ') .$request .$message ."\n", FILE_APPEND | LOCK_EX);
    } else {
      Debugger::log($request .$message);
    }

    if (self::$channel == 'ajax') {
      Debugger::fireLog($request .$message);
    }
  }

  // format request information for printing
  private static function renderRequest()
  {
    $record =  ' ' .$_SERVER['REMOTE_ADDR'] .' [' .implode('; ', self::times()) .'] ';

    // user agent
    if (php_sapi_name() == "cli") {
      $record .= 'Command';
    } elseif (is_callable('parse_user_agent') && isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = parse_user_agent();
      $record .= $agent['browser'] .' ' .strstr($agent['version'], '.', true); // .' on ' .$agent['platform'];
    }

    return $record .' ' .$_SERVER['REQUEST_METHOD'];
  }

  // format message collection foor printing
  private static function renderMessage()
  {
    $record = '';
    if (self::$collection) {
      foreach (self::$collection as $item) {
        $record .= "\n\t" .$item['message'] .'  ' .$item['data'];
      }
      self::$collection = '';
    }
    return $record;
  }

  // set job name
  public static function job(string $name)
  {
    self::$jobStack[] = $name;
    if (@self::$durations[$name]['start'])
      throw new \InvalidArgumentException("Job $name double start.");

    self::$durations[$name]['start'] = microtime(true);
    if (!isset(self::$durations[$name]['duration'])) {
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

  // get time duration in user friendly format
  // argument in milliseconds
  	public static function easyTime(float $duration): string
  	{
      if ($duration < .9) {
        return round(1000 * $duration) .' ms';
      } elseif ($duration < 9.) {
        return round($duration, 2) .' s';
      } elseif ($duration < 90.) {
        return round($duration, 1) .' s';
      } else {
        return round($duration/60, 1) .' min';
      }
  	}

}
