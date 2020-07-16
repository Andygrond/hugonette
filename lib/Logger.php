<?php

namespace Andygrond\Hugonette;

/* PSR-3 compatible logger for Hugonette
 * Tracy debugger is utilized in modes 'prod' and 'dev'
 *
 * @author Andygrond 2020
 * Optional dependency: https://github.com/nette/tracy
 * Optional dependency: https://github.com/donatj/PhpUserAgent
**/

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Logger
{
  // message level hierachy
  private $levels = [
    'debug'     => 10, // Detailed debug information
    'info'      => 20, // Interesting events
    'notice'    => 30, // Normal but significant events
    'warning'   => 40, // Exceptional occurrences that are not errors
    'error'     => 50, // Runtime errors that do not require immediate action but should typically be logged and monitored
    'critical'  => 60, // Critical conditions
    'alert'     => 70, // Action must be taken immediately - this should trigger the SMS alerts
    'emergency' => 80, // System is unusable
  ];

  private $collection = []; // messages waiting for output
  private $minLevel = 0;    // lowest level of logged messages
  private $logFile = '';    // path to log filename
  private $sendFireLog = false; // switch to sending messages to Chrome console

  public $mode;        // Tracy debugger working mode

  /* log initialization
  @ $path = /path/to/log/filename.log or /path/to/log/folder/
  File with obligatory .log extension - uses Hugonette log format
  When directory is given - uses Tracy native logger
  @ $mode - set debugger mode ['plain'|'prod'|'dev']
  Tracy debugger will not be used in 'plain' mode
  In 'prod' Tracy works in production mode
  In 'dev' Tracy works in development mode
  */
  public function __construct(string $path, string $mode = 'plain')
  {
    // set log dir and file
    if (strrchr($path, '.') == '.log') {
      $this->logFile = $path;
      $logPath = dirname($path) .'/';
      ini_set('error_log', $path);
    } else {
      $logPath = rtrim($path, '/') .'/';
    }

    $this->mode = $mode;
    if ($mode != 'plain') {
      $tracyMode = ($mode == 'dev')? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
      Debugger::enable($tracyMode, $logPath);
    }
  }

  // destruction of Logger instance will write the $collection to log file
  public function __destruct()
  {
    $this->flush();
  }

  // all log level messages goes here
  // @level - PSR-3 level
  // @args = [$message, $context]
  public function __call(string $level, array $args)
  {
    [$message, $context] = array_pad($args, 2, []);
    $this->log($level, $message, $context);
  }

  // logs with an arbitrary level
  public function log(string $level, $message, $context = [])
  {
    if (!$levelNo = @$this->levels[$level]) {
      Log::trigger('Log method not found: ' .$level);
    } elseif ($levelNo >= $this->minLevel) {  // message filtering
      $this->collection[] = [
        'level' => strtoupper($level),
        'message' => $message,
        'context' => $context,
      ];
    }
  }

  // set lowest level of registered messages
  public function minLevel(string $level)
  {
    if (isset($this->levels[$level])) {
      $this->minLevel = $this->levels[$level];
    } else {
      Log::trigger("Log level: $level is not valid.");
    }
  }

  // add extra level $name
  // @ $name and $weight cannot overlap with existing
  public function addLevel(string $name, int $weight)
  {
    if (!array_search($weight, $this->levels) && !isset($this->levels[$name])) {
      $this->levels[$name] = $weight;
    }
  }

  // shortcuts to enable special functions
  // can be applied only when Tracy is active
  public function enable(string $name)
  {
    if ($mode != 'plain') {
      switch($name) {
        case 'ajax':   // log to Chrome console with FireLogger extension
          $this->sendFireLog = true;
          break;
        case 'output': // enable OutputDebugger
          OutputDebugger::enable();
          break;
      }
    }
  }

  // write $collection to file
  private function flush()
  {
    $formatter = new LogFormatter;
    $message = $formatter->message($this->collection);

    if ($this->logFile) {
      file_put_contents($this->logFile, $formatter->date() .$message ."\n", FILE_APPEND | LOCK_EX);
    } else {
      Debugger::log($message);
    }

    if ($this->sendFireLog) {
      Debugger::fireLog($message);
    }
  }

}
