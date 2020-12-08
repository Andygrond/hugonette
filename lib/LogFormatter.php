<?php

namespace Andygrond\Hugonette;

/* Log formatter for Hugonette
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Helpers\Browser;

class LogFormatter
{
  protected $botsDefFile; // Opional bots definitions

  public function __construct($botsDefFile = null)
  {
    $this->botsDefFile = $botsDefFile;
  }

  // format date part of log message
  public function date(): string
  {
    [$sec, $msec] = explode('.', sprintf('%.3F', $_SERVER["REQUEST_TIME_FLOAT"]));
    return date('Y-m-d H:i:s.', $sec) .$msec .' ';
  }

  // format message collection
  public function message(array &$collection): string
  {
    // general info
    if (end($collection)['message'] == 'Duration') {
      $duration = array_pop($collection);
      $record = $this->generalInfo($duration['context']);
    } else {
      $record = $this->generalInfo();
    }

    // message collection
    if ($collection) {
      foreach ($collection as $item) {
        $record .= "\n\t[" .$item['level'] .'] ' .$item['message'];
        if ($item['context']) {
          $record .= ' ' .json_encode($item['context'], JSON_UNESCAPED_UNICODE);
        };
      }
      $collection = '';
    }
    return $record;
  }

  // format general information part
  private function generalInfo(array $duration = null): string
  {
    if (php_sapi_name() == 'cli') {
      if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $record = 'Windows CLI';  // $_SERVER['TERM'] is not defined on Win
      } else {
        $record = isset($_SERVER['TERM'])? 'Shell CLI' : 'Cron CLI';
      }
    } else {
      $record = $_SERVER['REMOTE_ADDR'] .' ' .Browser::name() .' ' .$_SERVER['REQUEST_METHOD'] .' ' .$this->pageURI();
    }
    if ($duration) {
      $record .= ' [' .implode(',', $duration) .']';
    }
    return $record;
  }

  // collect actual page address
  private function pageURI(): string
  {
    $ssl = (@$_SERVER['HTTPS'] == 'on');
    $link = ($ssl? 'https://' : 'http://') .$_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != ($ssl? '443' : '80')) {
      $link .= ':' .$_SERVER["SERVER_PORT"];
    }
    return $link .urldecode($_SERVER['REQUEST_URI']);
  }

}
