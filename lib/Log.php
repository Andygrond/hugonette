<?php

namespace Andygrond\Hugonette;

/* Log Facade for Hugonette
 * Any PSR-3 compatible logger can be injected
 * When working with native Logger adds extra level 'view' to collect view messages
 * Tracy debugger is utilized in modes 'prod' and 'dev'
 *
 * @author Andygrond 2020
 * todo: mailing and ajax channel test
**/

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Log
{
  private static $jobStack = [];  // job names stack
  private static $duration;       // Duration object
  private static $debug = 'plain';// Debugger mode
  private static $sendFireLog;    // Chrome FireLog console enabled

  public static $viewMessages = []; // messages collected to be passed to view
  public static $logger;          // Logger object

  public static function set(Logger $logger)
  {
    if (method_exists($logger, 'addLevel')) {
      $logger->addLevel('view', 35);  // level 'view' for sending messages to view
    }
    self::$logger = $logger;
    self::$duration = new Duration;
  }

  // output the message - Log must be set prior to calling this
  // $args = [record, data]
  public static function __callStatic(string $level, array $args)
  {
    if (!self::$logger) {
      return;
    }
    [$message, $context] = array_pad($args, 2, null);
    if ($level == 'view') {  // collect view errors, which can be attached to model
      self::$viewMessages[] = $message;
    }
    if (self::$jobStack) {
      $message = end(self::$jobStack) .': ' .$message;
    }

    if (self::$sendFireLog) {
      Debugger::fireLog($message);
    }
    self::$logger->log($level, $message, $context);
  }

  // put all collected messages to log file
  public static function close()
  {
    if (self::$logger) {
      self::$logger->debug('Duration', self::$duration->timeLen());
      self::$logger->flush();
    }
  }

  // set job name
  // names reserved: [pre] for preprocessing and [run] for runtime
  public static function job(string $name)
  {
    self::$jobStack[] = $name;
    self::$duration->start($name);
  }

  // quit current job
  // reset old job name and save job duration
  public static function done(string $name)
  {
    $lastName = self::$jobStack? array_pop(self::$jobStack) : '#';
    if ($name == $lastName) {
      self::$duration->stop($name);
    } else {
      self::trigger("Job $name cannot be done, $lastName is waiting. Simple job nesting allowed only.");
    }
  }

  // initialize Tracy debugger in @mode ['prod'|'dev']
  public static function tracy(string $mode)
  {
    if ($mode == 'plain') {
      self::trigger("Debugger can not be put back into Plain mode.");
    } else {
      self::$debug = $mode;
      $tracyMode = ($mode == 'dev')? Debugger::DEVELOPMENT : Debugger::PRODUCTION;
      Debugger::enable($tracyMode, self::$logger->logPath);
    }
  }

  // shortcuts to enable special debugger functions
  // can be applied only when Tracy is active
  public static function enable(string $name)
  {
    if (self::$logger && $this->debug != 'plain') {
      switch($name) {
        case 'ajax':   // log to Chrome console with FireLogger extension
          self::$sendFireLog = true;
          break;
        case 'output': // enable OutputDebugger
          OutputDebugger::enable();
          break;
      }
    }
  }

  // measured time lengths
  public static function times()
  {
    self::$duration->timeLen();
  }

  // trigger PHP notice with caller identification
  public static function trigger($message)
  {
    if ($caller = @debug_backtrace()[2]) {
     $message .= ' Called in ' .$caller['function'].' from ' .$caller['file'] .':' .$caller['line'];
    }
    trigger_error($message);
  }
}
