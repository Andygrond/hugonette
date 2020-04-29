<?php

namespace Andygrond\Hugonette;

/* Page state attributes for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  public $trialCounter = 0; // counter of route trials
  private $attrib = [
//===== must be declared
    'publishBase' => null,  // path to rendered static site (Hugo public/ folder)
//    'tempDir' => null,    // path to cache used in view mode = latte
//===== defaults can be altered
    'view' => 'plain',      // view mode [ plain | latte | json | file | inline ]
    'presenterNamespace' => 'App\Presenters',
//===== not configurable - will be calculated
//    'pathInfo' => null,    // full request URI without GET args
//    'requestBase' => null, // group part of request URI
//    'template' => null,    // template file name
//    'request' => [],       // request URI args with [0] variable part of request URI
//    'route' => [],         // recorded route passes
  ];

  public function __construct(array $attributes)
  {
    $this->attrib = $attributes + $this->attrib;

    // base string for current route group (subfolder of document root)
    $this->attrib['requestBase'] = dirname($_SERVER['SCRIPT_NAME']);
    // http request method
    $this->attrib['httpMethod'] = strtolower($_SERVER['REQUEST_METHOD']);

    // safe PATH_INFO calculation
    [ $pathInfo ] = explode('?', $_SERVER['REQUEST_URI']);
    $this->attrib['pathInfo'] = rtrim($pathInfo, '/') .'/';

    $this->setRequest();
  }

  // run presenter instance and exit if truly presented
  // private function runPresenter(string $presenter, bool $static = false)
  public function run(string $presenter)
  {
    $this->attrib['route'][$this->trialCounter] = $presenter; // route tracer
    PresenterFactory::create($presenter, $this->attrib);
  }

  // redirect to another URL
  // @$permanent true = 301 Moved Permanently; false = 302 Found
  public function redirect(string $to, bool $permanent)
  {
    if ($to[0] != '/' && strpos($to, '//') === false) {
      $to = $this->attrib['publishBase'] .$to;
    }

    $code = $permanent? 301 : 302;
    Log::info($code .' Redirected to: ' .$to);
    header('Location: ' .$to, true, $code);

    exit;
  }

  // calculate template file name based on the URL
  // return = file exists
  private function template(): bool
  {
    $this->attrib['template'] = $this->$this->attrib['request'][0] .'/index.html';
    return is_file($this->attrib['publishBase'] .$template);
  }

  /*/ get a named page attribute
  public function get($name)
  {
    return $this->attrib[$name];
  }*/

  // get initial array of page attributes and update them to group values
  public function updateAttributes(string $pattern, array $attributes = []): array
  {
    $arch = $this->attrib;
    if (count($attributes)) {
      $this->attrib = $attributes + $arch;
    }
    $this->setRequest($pattern);
    return $arch;
  }

  // set full array of page attributes
  public function refreshAttributes(array $attrib)
  {
    $this->attrib = $attrib;
  }

  // checking http request method
  public function checkMethod(string $method): bool
  {
    return ($this->attrib['httpMethod'] == $method);
  }

  // check matching URL from left
  public function exactMatch(string $pattern): bool
  {
    return (strpos($this->attrib['requestPath'], $pattern) === 0);
  }

  // check regular expression pattern matching
  // replace page params according to the pattern
  public function regMatch(string $pattern): bool
  {
    $pattern = '@^' .$pattern .'$@';
    if (preg_match($pattern, $this->attrib['requestPath'], $params) === 1) {
      $this->attrib['request'] = $params;
      return true;
    }
    return false;
  }

  // simple pattern matching test - no variable parts
  // calculate requestBase for a group and request URI variables
  private function setRequest(string $groupBase = '')
  {
    if ($groupBase) {
      $this->attrib['requestBase'] .= $groupBase;
    }

    $path = $this->attrib['pathInfo'];
    if ($this->attrib['requestBase']) {
      $path = substr($path, strlen($this->attrib['requestBase']));
    }
    $this->attrib['request'] = explode('/', $path);
    $this->attrib['request'][0] = $path;
  }

}
