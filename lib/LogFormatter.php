<?php

namespace Andygrond\Hugonette;

/* Log formatter for Hugonette
 * @author Andygrond 2020
 * Dependency: https://github.com/donatj/PhpUserAgent
**/

class LogFormatter
{
  public function __construct()
  {
  }

  // format date part of log message
  public function date(): string
  {
    [$sec, $msec] = explode('.', sprintf('%.3F', $_SERVER["REQUEST_TIME_FLOAT"]));
    return date('Y-m-d H:i:s.', $sec) .$msec;
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
        $record .= "\n\t" .$item['level'] .' ' .$item['message'];
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
    $record = ' ' .$_SERVER['REMOTE_ADDR'] .' ' .$this->userAgent();

    if ($duration) {
      $record .= ' [' .implode(',', $duration) .']';
    }
    return $record .' ' .$_SERVER['REQUEST_METHOD'] .' ' .$this->pageURI();
  }

  // format user agent
  private function userAgent(): string
  {
    if (is_callable('parse_user_agent') && isset($_SERVER['HTTP_USER_AGENT'])) {
      if ($agent = parse_user_agent()) {
        return $agent['browser'] .' ' .strstr($agent['version'], '.', true); // .' on ' .$agent['platform'];
      } else {
        return strtoupper(php_sapi_name());
      }
    }
    return '';
  }

  // collect actual page address
  private function pageURI(): string
  {
    $ssl = (@$_SERVER['HTTPS'] == 'on');
    $link = ($ssl? 'https://' : 'http://') .$_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != ($ssl? '443' : '80')) {
      $link .= ':' .$_SERVER["SERVER_PORT"];
    }
    return $link .$_SERVER['REQUEST_URI'];
  }

}
