<?php

namespace Andygrond\Hugonette;

/** PSR-3 compatible logger for Hugonette
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Helpers\LogFormatter;
use Andygrond\Hugonette\Helpers\LogArchiver;

class Logger
{
  // message level hierachy
  private $levels = [
    'debug'     => 10, // Detailed debug information
    'info'      => 20, // Interesting events
    'notice'    => 30, // Normal but significant events
    'warning'   => 40, // Exceptional occurrences that are not errors
    'error'     => 50, // Runtime errors that do not require immediate action but should be monitored
    'critical'  => 60, // Critical conditions
    'alert'     => 70, // Action must be taken immediately - this should trigger the SMS alerts
    'emergency' => 80, // System is unusable
  ];

  private $collection = []; // messages waiting for output
  private $minLevel = 0;    // lowest level of logged messages
  private $formatter;       // Formatter object
  
  public $logFile = '';    // path to log filename
//  public $logPath;          // path to log

  /** log initialization
  * @param filename path to log file or folder relative to system log folder
  * @param filesize max size in megabytes
  * @param cut max number of archived files
  * File with obligatory .log extension - uses Hugonette log format
  * When directory is given - uses Tracy native logger
  */
  public function __construct(string $filename, float $filesize = 1, int $cut = 10)
  {
    $this->formatter = new LogFormatter;
    $filename or $filename = 'hugonette_app.log';
    $this->logFile = $path = Env::get('base.system') .'/log/' .$filename;

    if (!file_exists($path)) {
      if (@touch($path)) {
        chmod($path, 0666); // for cron and CLI obviously
      } else {
        $this->logFile = '';
      }
    } elseif (filesize($path)/1024 > 1024*$filesize) {
      (new LogArchiver($path))->shift($cut);
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
    $levelNo = $this->levels[$level]?? 100;
      
    if ($levelNo >= $this->minLevel) {  // message filtering
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
    $this->minLevel = (int) $level;
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
    if ($this->collection && $this->logFile) {
      $message = $this->formatter->date() .$this->formatter->message($this->collection) ."\n";
      file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);
    }
  }

}
