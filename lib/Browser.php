<?php

namespace Andygrond\Hugonette;

/* Simple and optimized browser detector
 * Attention: agent name is roughly estimated and can be not always accurate
 * @author Andygrond 2020
**/

class Browser
{
  public $browserList = [
  // Humans
    'bot'      => 'Bot',
    'http'     => 'Bot',
    'opera'    => 'Opera',
    'opr/'     => 'Opera',
    'edge'     => 'Edge',
    'chrome'   => 'Chrome',
    'safari'   => 'Safari',
    'firefox'  => 'Firefox',
    'msie'     => 'MSIE',
    'trident/7'=> 'MSIE',
  ];

  protected $agent;  // user agent string
  protected $found = [];  // values found so far

  public function __construct(string $agent = null)
  {
    $this->agent = strtolower($agent?? @$_SERVER['HTTP_USER_AGENT']);
  }

  // get array of user preferred languages
  public function getLangs(array $siteLangs = null): array
  {
    return explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
  }

  // @siteLangs array of 2-letter available language codes
  public function getBestLang(array $siteLangs): string
  {
    foreach($this->getLangs as $lang) {
      $lang = substr($lang, 0, 2);
      if (in_array($lang, $siteLangs)) {
        return $lang;
      }
    }

  }

  // get type of user: [ human | bot | unknown ]
  public function getType(): string
  {
    if (!isset($this->found['type'])) {
      $this->detect();
    }
    return $this->found['type'];
  }

  // get name of the browser or bot
  public function getName(): string
  {
    if (!isset($this->found['name'])) {
      $this->detect();
    }
    return $this->found['name'];
  }

  // detect browser type and name
  // bot list path can be specified here
  public function detect(string $botListPath = null)
  {
    if ($this->agent) {
      if ($this->findAgent($this->browserList)) {
        $this->found['type'] = 'human';
      } else {
        $botList = parse_ini_file($botListPath?? __DIR__ .'/bots.ini');
        if ($this->findAgent($botList)) {
          $this->found['type'] = 'bot';
        } else {
          $this->found['type'] = 'unknown';
          $this->found['name'] = 'Unknown:' .$_SERVER['HTTP_USER_AGENT'];
        }
      }
    } else {
      $this->found['type'] = 'api';
      $this->found['name'] = 'API:' .strtoupper(php_sapi_name());
    }
  }

  // find pattern in the array
  protected function findAgent(array $agentList): bool
  {
    $agent = ' ' .$this->agent;
    foreach ($agentList as $pattern => $name) {
      if (strpos($agent, $pattern)) {
        $this->found['name'] = $name;
        return true;
      }
    }
    return false;
  }

}
