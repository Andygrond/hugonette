<?php

namespace Andygrond\Hugonette;

/* Simple and optimized browser detector
 * Attention: agent name is roughly estimated and can be not always accurate
 * @author Andygrond 2020
**/

class Browser
{
  private static $found = [];  // browser type and name

  // get array of user preferred languages
  public static function langs(array $siteLangs = null): array
  {
    return explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  }

  // @siteLangs array of 2-letter available language codes
  public static function bestLang(array $siteLangs): string
  {
    foreach(self::langs() as $lang) {
      $lang = substr($lang, 0, 2);
      if (in_array($lang, $siteLangs)) {
        return $lang;
      }
    }
  }

  // get type of user
  public static function type(): string
  {
    if (!self::$found) {
      self::detectAgent();
    }
    return self::$found['type'];
  }

  // get name of the browser or bot
  public static function name(): string
  {
    if (!self::$found) {
      self::detectAgent();
    }
    return self::$found['name'];
  }

  // detect browser type and name
  private static function detectAgent()
  {
    if ($agent = @$_SERVER['HTTP_USER_AGENT']) {
      if (!self::findAgent($agent)) {
        self::$found = [
          'type' => 'Unknown',
          'name' => $agent,
        ];
      }
    } else {
      self::$found = [
        'type' => 'API',
        'name' => strtoupper(php_sapi_name()),
      ];
    }
  }

  // find pattern in the array
  private static function findAgent($agent): bool
  {
    $agent = strtolower($agent);
//    $botsDef = Page::env('bots'); TODO
    $botsDef = __DIR__ .DIRECTORY_SEPARATOR .'Data' .DIRECTORY_SEPARATOR .'bots.ini';
    $agentList = parse_ini_file($botsDef, true);

    foreach ($agentList as $type => $alist) {
      foreach ($alist as $pattern => $name) {
        if (strpos($agent, $pattern) !== false) {
          self::$found = [
            'type' => $type,
            'name' => $name,
          ];
          return true;
        }
      }
    }
    return false;
  }

}
