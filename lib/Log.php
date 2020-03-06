<?php

namespace Andygrond\Hugonette;

/* Simple logger for Tracy debugger
* @author Andygrond 2019
* Dependency: https://github.com/nette/tracy
* Optional dependency: https://github.com/donatj/PhpUserAgent
**/

// todo: error levels and mailing: https://doc.nette.org/en/2.1/debugging
// todo: send job $elapsed time somewhere

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Log
{
  // allowed names of message
  private static $allowedTypes = [ 'error', 'warning', 'view', 'info' ];

  public static $viewErrors = [];   // messages collected to be passed to view
  private static $collection = [];  // messages waiting for output to file
  private static $jobStack = [];    // job names stack
  private static $isActive = false; // log is active

  // log initialization
  // set log folder and Tracy debugger mode ['dev' | 'prod']
  public static function set(string $logDir, string $mode = 'prod')
  {
    $logMode = strncasecmp($mode, 'dev', 3)? Debugger::PRODUCTION : Debugger::DEVELOPMENT;
    Debugger::enable($logMode, $logDir);
    self::$isActive = true;

    // Debugger::$strictMode = true;  // log all error types
    // Debugger::$logSeverity = E_NOTICE | E_WARNING | E_USER_WARNING;  // log html screens
    // Debugger::dispatch();  // do it after session reloading
  }

  // for mailing feature (not done yet)
  public static function email(string $email)
  {
    Debugger::$email = $email;
  }

  // output the message
  // $args = [record, data]
  public static function __callStatic(string $type, array $args)
  {
    if (in_array($type, self::$allowedTypes)) {
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
  public static function job(string $name)
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
    if (!self::$isActive) {
      return;
    }

    $record = self::formatTimeframe() .$_SERVER['REMOTE_ADDR'];

    if (php_sapi_name() == "cli") {
      $record .= ' Command Line';
    } elseif (is_callable('parse_user_agent') && isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = parse_user_agent();
      $record .= ' ' .$agent['browser'] .' ' .strstr($agent['version'], '.', true); // .' on ' .$agent['platform'];
    }

    if (self::$collection) {
      foreach (self::$collection as $item) {
        $record .= "\n" .$item['message'] .' | ' .$item['data'];
      }
      self::$collection = '';
    }

    $record .= ' ' .$_SERVER['REQUEST_METHOD'];
    Debugger::log($record);
  }

  private function formatTimeframe(string $name = null): string
  {
    if ($gap = Debugger::timer($name)) {
      $gap = 1000*round($gap, 3);
      return "[$gap ms] ";
    } else {
      return "[n/a] ";
    }
  }

}
