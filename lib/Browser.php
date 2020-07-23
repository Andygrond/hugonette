<?php

namespace Andygrond\Hugonette;

/* Simple and configurable browser detector
 * @author Andygrond 2020
 * inspired by August R. Garcia & Francesco R
**/

class Browser
{
  public $browserNames = [
  // Humans
    'opera'    => 'Opera',
    'opr/'     => 'Opera',
    'edge'     => 'Edge',
    'chrome'   => 'Chrome',
    'safari'   => 'Safari',
    'firefox'  => 'Firefox',
    'msie'     => 'MSIE',
    'trident/7'=> 'MSIE',

  // Search Engines
    'google'   => 'Bot-Googlebot',
    'bing'     => 'Bot-Bingbot',
    'slurp'    => 'Bot-Yahoo!-Slurp',
    'duckduck' => 'Bot-DuckDuckBot',
    'baidu'    => 'Bot-Baidu',
    'yandex'   => 'Bot-Yandex',
    'sogou'    => 'Bot-Sogou',
    'exabot'   => 'Bot-Exabot',
    'msn'      => 'Bot-MSN',

  // Common Bots
    'mj12bot'  => 'Bot-Majestic',
    'ahrefs'   => 'Bot-Ahrefs',
    'semrush'  => 'Bot-SEMRush',
    'rogerbot' => 'Bot-OpenSiteExplorer',
    'dotbot'   => 'Bot-OpenSiteExplorer',
    'frog'     => 'Bot-Screaming-Frog',
    'screaming'=> 'Bot-Screaming-Frog',
    'blex'     => 'Bot-BLEXBot',

  // Miscellaneous
    'facebook' => 'Bot-Facebook',
    'pinterest'=> 'Bot-Pinterest',
    'crawler'  => 'Bot',
    'api'      => 'Bot',
    'spider'   => 'Bot',
    'http'     => 'Bot',
    'bot'      => 'Bot',
    'archive'  => 'Bot',
    'info'     => 'Bot',
    'data'     => 'Bot',
    'php'      => 'PHP',
  ];

  public function name()
  {
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $agent = ' ' .strtolower($_SERVER['HTTP_USER_AGENT']);
      foreach ($this->browserNames as $pattern => $name) {
        if (strpos($agent, $pattern)) {
          return $name;
        }
      }
      return 'Unknown:' .$_SERVER['HTTP_USER_AGENT'];
    } else {
      return 'API:' .strtoupper(php_sapi_name());
    }
  }

}
