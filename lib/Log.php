<?php

namespace Andygrond\Hugonette;

/** Log Facade for Hugonette
 * Any PSR-3 compatible logger can be injected
 * When working with native Logger adds extra level 'view' to collect view messages
 * Optional dependency: https://github.com/nette/tracy
 * Tracy debugger is utilized in 'production' or 'development' mode
 *
 * @author Andygrond 2020
 * todo: ajax channel test
**/

use Tracy\Debugger;
use Tracy\OutputDebugger;
use Andygrond\Hugonette\Helpers\Duration;

class Log
{
  private static $logger;         // Logger object
  private static $duration;       // Duration object
  private static $jobStack = [];  // job names stack
  private static $sendFireLog;    // Chrome FireLog console enabled

  public static $debugMode = 'plain';   // Debugger mode [plain|tracy]
  public static $viewMessages = []; // messages collected to be passed to view

  /**
  * @param $logger - PSR-3 or beyond compatible logger object
  */
  public static function set($logger = null)
  {
    if (is_object($logger)) {
      if (method_exists($logger, 'addLevel')) {
        $logger->addLevel('view', 35);  // create level 'view' for sending messages to view
      }
      self::$logger = $logger;
      if ($logger->logFile) {
        ini_set('error_log', $logger->logFile);
      }
    }
    self::$duration = new Duration;
  }

  /** output the message - Log must be set prior to calling this function
  * @param $args = [record, data]
  */
  public static function __callStatic(string $level, array $args)
  {
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
    if (self::$logger) {
      self::$logger->log($level, $message, $context);
    } else {
      Debugger::log($message);
    }
  }

  // put all collected messages to log file
  public static function close()
  {
    if (self::$logger) {
      self::$logger->debug('Duration', self::$duration->timing());
      self::$logger->flush();
    }
  }

  /** start job
  * @param $name - name of the job
  * reserved names: [pre] for preprocessing and [run] for runtime
  */
  public static function job(string $name)
  {
    self::$jobStack[] = $name;
    self::$duration->start($name);
  }

  /** quit current job, reset old job name and save job duration
  * @param $name - name of the job
  */
  public static function done(string $name)
  {
    $lastName = self::$jobStack? array_pop(self::$jobStack) : '#';
    if ($name == $lastName) {
      self::$duration->stop($name);
    } else {
      self::$logger->log('warning', "Job $name is interlacing with $lastName which should be ended first using Log::done('$lastName')");
    }
  }

  /** shortcuts to enable special debugger functions
  * once enabled function can not be disabled
  * @param $name - name of the functionality
  */
  public static function enable(string $name)
  {
    switch($name) {
      case 'tracy':  // enable Tracy in given mode
        if (self::$logger) {
          self::$debugMode = 'tracy';
          $tracyMode = (Env::get('mode') == 'production')? Debugger::PRODUCTION : Debugger::DEVELOPMENT;
          $logPath = dirname(self::$logger->logFile) .'/';
          Debugger::enable($tracyMode, $logPath);
        }
        break;

      case 'ajax':   // log to Chrome console with FireLogger extension - Tracy must be active
        if (self::$logger && self::$debugMode == 'tracy') {
          self::$sendFireLog = true;
        }
        break;

      case 'output': // enable OutputDebugger
        OutputDebugger::enable();
        break;
    }
  }

  // measured time lengths
  public static function timing()
  {
    return self::$duration->timing();
  }

  // prevented instantiating
  private function __construct(){}

  // prevented cloning
  private function __clone(){}

  // prevented unserialization
  public function __wakeup(){}

}
