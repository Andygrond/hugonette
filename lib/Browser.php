<?php

namespace Andygrond\Hugonette;

/* Simple and optimized browser detector
 * Attention: agent name is roughly estimated and can be not always accurate
 * @author Andygrond 2020
**/

class Browser
{
  protected $agent;       // user agent string lowercase
  protected $botsDefFile; // optional bots definitions filename
  protected $found = [];  // type and name found

  public function __construct(string $botsDefFile = null)
  {
    $this->botsDefFile = $botsDefFile?? __DIR__ .'/Data/bots.ini';
    $this->agent = strtolower(@$_SERVER['HTTP_USER_AGENT']);
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

  // get type of user
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
  protected function detect()
  {
    if ($this->agent) {
      if (!$this->findAgent()) {
        $this->found = [
          'type' => 'Unknown',
          'name' => $_SERVER['HTTP_USER_AGENT'],
        ];
      }
    } else {
      $this->found = [
        'type' => 'API',
        'name' => strtoupper(php_sapi_name()),
      ];
    }
  }

  // find pattern in the array
  protected function findAgent(): bool
  {
    $agentList = parse_ini_file($this->botsDefFile, true);

    foreach ($agentList as $type => $alist) {
      foreach ($alist as $pattern => $name) {
        if (strpos($this->agent, $pattern) !== false) {
          $this->found = [
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
