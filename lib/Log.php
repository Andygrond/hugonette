<?php

namespace Andygrond\Hugonette;

/* Log Facade for Hugonette
 * Any PSR-3 compatible logger can be injected
 * When working with native Logger adds extra level 'view' to collect view messages
 * Tracy debugger is utilized in 'production' or 'development' mode
 *
 * @author Andygrond 2020
 * todo: ajax channel test
**/

use Tracy\Debugger;
use Tracy\OutputDebugger;

class Log
{
  private static $jobStack = [];  // job names stack
  private static $duration;       // Duration object
  private static $sendFireLog;    // Chrome FireLog console enabled

  public static $debug = 'plain';   // Debugger mode
  public static $viewMessages = []; // messages collected to be passed to view
  public static $logger;            // Logger object

  /**
  * @param logger - PSR-3 compatible logger object
  */
  public static function set(Logger $logger)
  {
    // create level 'view' for sending messages to view
    if (method_exists($logger, 'addLevel')) {
      $logger->addLevel('view', 35);
    }
    self::$logger = $logger;
    self::$duration = new Duration;
  }

  /** output the message - Log must be set prior to calling this function
  * @param args = [record, data]
  */
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

  /** start job
  * @param name - name of the job
  * reserved names: [pre] for preprocessing and [run] for runtime
  */
  public static function job(string $name)
  {
    self::$jobStack[] = $name;
    self::$duration->start($name);
  }

  /** quit current job, reset old job name and save job duration
  * @param name - name of the job
  */
  public static function done(string $name)
  {
    $lastName = self::$jobStack? array_pop(self::$jobStack) : '#';
    if ($name == $lastName) {
      self::$duration->stop($name);
    } else {
      self::trigger("Job $name cannot be done, $lastName is waiting. Job interlacing is not allowed.");
    }
  }

  /** initialize Tracy debugger
  * @param mode ['prod'|'dev']
  */
  public static function tracy(string $mode)
  {
  }

  /** shortcuts to enable special debugger functions
  * once enabled function can not be disabled
  *
  * @param name - name of the functionality
  */
  public static function enable(string $name)
  {
    switch($name) {
      case 'tracy':  // enable Tracy in given mode
        if (self::$logger) {
          self::$debug = Env::get('mode');
          $tracyMode = (self::$debug == 'production')? Debugger::PRODUCTION : Debugger::DEVELOPMENT;
          Debugger::enable($tracyMode, self::$logger->logPath);
        }
        break;

      case 'ajax':   // log to Chrome console with FireLogger extension - Tracy must be active
        if (self::$logger && self::$debug != 'plain') {
          self::$sendFireLog = true;
        }
        break;

      case 'output': // enable OutputDebugger
        OutputDebugger::enable();
        break;
    }
  }

  // measured time lengths
  public static function times()
  {
    self::$duration->timeLen();
  }

  /** trigger PHP notice with caller identification
  * @param message - output error message
  */
  public static function trigger($message)
  {
    if ($caller = @debug_backtrace()[2]) {
     $message .= ' Called in ' .$caller['function'].' from ' .$caller['file'] .':' .$caller['line'];
    }
    trigger_error($message);
  }
}
