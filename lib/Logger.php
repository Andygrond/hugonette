<?php

namespace Andygrond\Hugonette;

/* PSR-3 compatible logger for Hugonette
 * @author Andygrond 2020
 * Optional dependency: https://github.com/nette/tracy
**/

use Tracy\Debugger;

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
  private $formatter;       // Formatter object

  public $logPath;          // path to log

  /** log initialization
  * @param filename path to log file or folder relative to system log folder
  * File with obligatory .log extension - uses Hugonette log format
  * When directory is given - uses Tracy native logger
  */
  public function __construct(string $filename)
  {
    $this->formatter = new LogFormatter;
    // set log dir and file
    $path = Env::get('base.system') .'/log/' .$path;
    if (strrchr($path, '.') == '.log') {
      $this->logFile = $path;
      $this->logPath = dirname($path) .'/';
      ini_set('error_log', $path);
    } else {
      $this->logPath = rtrim($path, '/') .'/';
    }
  }

  // destruction of Logger instance will write the $collection to log file
  public function __destruct()
  {
    $this->flush();
  }

  /**
  * all log level messages goes here
  * @param level - PSR-3 level
  * @param args = [$message, $context]
  */
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

  // write $collection to file
  public function flush()
  {
    if ($this->collection) {
      $message = $this->formatter->message($this->collection);

      if ($this->logFile) {
        file_put_contents($this->logFile, $this->formatter->date() .$message ."\n", FILE_APPEND | LOCK_EX);
      } else {
        Debugger::log($message);
      }
    }
  }

}
