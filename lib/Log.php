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
    if (self::$logger) {
      throw new \BadMethodCallException("Log cannot be set twice");
    }
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
      self::$logger->debug('Duration', (array) self::$duration->timeLen());
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
    if (!self::$jobStack) {
      throw new \BadMethodCallException("No job started to be done");
    }
    $lastName = array_pop(self::$jobStack);
    if ($name != $lastName) {
      throw new \InvalidArgumentException("Job $name interlaces with another. Nesting allowed only.");
    }
    self::$duration->stop($name);
  }

}
