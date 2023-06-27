<?php

namespace Andygrond\Hugonette;

/** JSON logger for Hugonette (PSR-3 incompatible)
 * @author Andygrond 2023
**/

use Andygrond\Hugonette\Helpers\LogArchiver;

class JsonLogger
{
  private $collection = []; // messages waiting for output
  private $logFile = '';    // path to log filename

  /** log initialization
  * @param filename path to log file or folder relative to system log folder
  * @param filesize max size in megabytes
  * @param cut max number of archived files
  */
  public function __construct(string $filename, float $filesize = 30, int $cut = 0)
  {
    $path = Env::get('base.system') .'/log/' .$filename;
    if (!file_exists($path)) {
      if (!@touch($path)) {
        throw new \RuntimeException('Log file is unavailable: ' .$path);
      }
      chmod($path, 0666); // for cron and CLI obviously
    } elseif (filesize($path)/1024 > 1024*$filesize) {
      (new LogArchiver($path))->shift($cut);
    }

    $this->logFile = $path;
  }

  /**
  * @param message if string is given - will be logged as pure string before JSON code
  * @param context data structure - can be given as first param
  */
  public function event($message, $context = [])
  {
    if (is_string($message)) {
      $message .= "\t" .$this->log($context);
    } else {
      $message = $this->log($message);
    }
    $this->flush($message);
  }

  // prepare and encode data structure
  private function log($data)
  {
    $out = [];
    if (is_iterable($data)) {
      foreach ($data as $key => $val) {
        if ($val) {
          $out[$key] = $val;
        }
      }
      isset($out['time']) or $out['time'] = date('Y-m-d H:i:s');
    } else {
      $out = $data;
    }
    return json_encode($out, JSON_UNESCAPED_UNICODE);
  }

  // write message to file
  private function flush(string $message)
  {
    if ($this->logFile) {
      file_put_contents($this->logFile, $message ."\n", FILE_APPEND | LOCK_EX);
    }
  }

}
