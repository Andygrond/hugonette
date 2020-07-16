<?php

namespace Andygrond\Hugonette;

/* Log Facade for Hugonette
 * Any PSR-3 compatible logger can be injected
 * When working with native Logger adds extra level 'view' to collect view messages
 *
 * @author Andygrond 2020
 * todo: mailing and ajax channel test
**/

class Log
{
  private static $jobStack = []; // job names stack
  private static $duration;      // Duration object

  public static $viewErrors = [];// messages collected to be passed to view
  public static $logger;         // Logger object

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
    if ($level == 'view') {  // collect view errors, which can be attached to model
      self::$viewErrors[] = $record;
    }
    [$message, $context] = array_pad($args, 2, null);
    if (self::$jobStack) {
      $message = end(self::$jobStack) .': ' .$message;
    }

    self::$logger->log($level, $message, $context);
  }

  // put all collected messages to log file
  public static function close()
  {
    if (self::$logger) {
      self::$logger->debug('Duration', self::$duration->timeLen());
      self::$logger = null;
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
      self::trigger("Job $name false attempt to be done. Simple job nesting allowed only.");
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
