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
  public function date()
  {
    $locinfo = localeconv();
    $date = \DateTime::createFromFormat('U' .$locinfo['decimal_point'] .'u', (string) $_SERVER["REQUEST_TIME_FLOAT"]);
    return $date->format('Y-m-d H:i:s.v ');
  }

  // format message collection
  public function message(&$collection)
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
        $record .= "\n\t" .$item['level'] .' ' .$item['message'] .'  ' .$item['context'];
      }
      $collection = '';
    }
    return $record;
  }

  // format general information part
  private function generalInfo($duration)
  {
    $record = ' ' .$_SERVER['REMOTE_ADDR'];
    if ($duration) {
      $record .= ' [' .implode('; ', $duration) .'] ';
    }
    $record .= (php_sapi_name() == "cli")? 'Command' : $this->userAgent();

    return $record .' ' .$_SERVER['REQUEST_METHOD'] .' ' .$_SERVER['REQUEST_URI'];
  }

  // format user agent
  private function userAgent()
  {
    if (is_callable('parse_user_agent') && isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = parse_user_agent();
      return $agent['browser'] .' ' .strstr($agent['version'], '.', true); // .' on ' .$agent['platform'];
    }
    return '';
  }

}
