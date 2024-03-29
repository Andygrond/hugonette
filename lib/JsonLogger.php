<?php

namespace Andygrond\Hugonette;

/** JSON logger for Hugonette (PSR-3 incompatible)
 * @author Andygrond 2023
**/

use Andygrond\Hugonette\Helpers\LogArchiver;
use DateTime;

class JsonLogger
{
  public $logFile = '';    // path to log filename
  private $cliMode = false;

/** log initialization
  * @param $filename path to log file or folder relative to system log folder
  * @param $filesize max size in megabytes
  * @param $cut max number of archived files
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
  * log message will be preceded with this method name:$id
  * $context can be omitted
  * @param $name - name
  * @param $args = [$id, $data, $context]
  */
  public function __call(string $name, array $args)
  {
    $context = @$args[2]? $args[2] : [];
    $this->event($name .':' .@$args[0], $args[1]?? [], $context);
  }

/**
  * general event log message output
  * @param $message if string is given - will be logged before JSON code
  * @param $data data structure - can be given as first param
  * @param $event event name or context array - when omitted, $message is assumed to be empty
  */
  public function event($message, $data, $event = null)
  {
    if ($event === null) {
      $message = $this->prepare($message, $data);
    } else {
      $message .= "\t" .$this->prepare($data, $event);
    }
    $this->save($message);
  }

/** logs with an arbitrary level 
 * for compatibility with Log static class
 */
  public function log(string $level, $message, $data = [])
  {
    if ($level == 'request') {
      $this->request($message);
    } else {
      $this->event($level, $data, $message);
    }
  }

/** request log message output
  * @param $client client data 
  */
  public function request($clientInfo = [])
  {
    if (is_array($clientInfo)) {
      $client = $clientInfo;
    } else {
      $client['info'] = $clientInfo;
    }
    $client['agent'] = $this->agent();
    if (!$this->cliMode) {
      $client['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR']?? $_SERVER['REMOTE_ADDR'];
      $server = $this->serverInfo();
    }

    $data = [
      'time' => $this->time(),
      'caller' => $this->tracePoint(),
      'client' => $client,
      'server' => $server?? 'localhost',
    ];
    
    $this->save('request' ."\t" .json_encode($data, JSON_UNESCAPED_UNICODE));
  }

  // get caller function info
  protected function tracePoint():string
  {
    $trace = debug_backtrace();
    foreach($trace as $k => $t) {
      if (strpos($t['file'], 'hugonette') === false) {
        break;
      }
    }
    $t = $trace[++$k];
    return $t['class'] .'::' .$t['function'];
  }

  // prepare and encode data structure
  protected function prepare($data, $context): string
  {
    if (is_array($context)) {
      $out = $context;
    } else {
      $out['event'] = $context;
    }

    isset($out['time']) or $out['time'] = (new DateTime())->format('Y-m-d\TH:i:s.uP');
    $out['data'] = $this->noNulls($data);

    return json_encode($out, JSON_UNESCAPED_UNICODE);
  }

  // delete null values
  protected function noNulls($data)
  {
    if (is_iterable($data)) {
      $out = [];
      foreach ($data as $key => $val) {
        if ($val !== null) {
          $out[$key] = $val;
        }
      }
      return $out;
    } else {
      return $data;
    }
  }

  // write message to file
  protected function save(string $message)
  {
    if ($this->logFile) {
      file_put_contents($this->logFile, $message ."\n", FILE_APPEND | LOCK_EX);
    }
  }

  // get general request information
  public function serverInfo(): array
  {
    return [
      'method' => $_SERVER['REQUEST_METHOD'],
      'host' => $_SERVER['HTTP_HOST'],
      'port' => $_SERVER["SERVER_PORT"],
      'uri' => $_SERVER['REQUEST_URI'],
    ];
  }

  // get ISO request time
  protected function time(): string
  {
    $tfloat = $_SERVER["REQUEST_TIME_FLOAT"];
    $sec = floor($tfloat);
    $msec = (string) round($tfloat - $sec, 3);
    return date('Y-m-d\TH:i:s.', $sec) .substr($msec, 2);
  }

  // get general agent info
  protected function agent(): string
  {
    if (php_sapi_name() == 'cli') {
      $this->cliMode = true;
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return 'Windows CLI';  // $_SERVER['TERM'] is not defined on Win
      } else {
        return isset($_SERVER['TERM'])? 'Shell CLI' : 'Cron CLI';
      }
    }
    return $_SERVER['HTTP_USER_AGENT']?? 'API';
  }

  // static Log compatibility
  public function flush()
  {
  }

  // static Log compatibility
  public function addLevel()
  {
  }

}
