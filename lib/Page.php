<?php

namespace Andygrond\Hugonette;

/* Page state attributes for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  // page attributes
  public $attrib = [
    'view' => 'plain',    // view mode [ plain | latte | json | upload | redirect ]
    'presenterNamespace' => 'App\Presenters',
  ];

  private $requestPath; // base for pattern comparison
  private $httpMethod;  // http method lowercase

  public function __construct(string $sysDir)
  {
    [ $path ] = explode('?', $_SERVER['REQUEST_URI']);
    $this->requestPath = rtrim($path, '/');
    $this->httpMethod = strtolower($_SERVER['REQUEST_METHOD']);

    $request = dirname($_SERVER['SCRIPT_NAME']);
    $this->attrib['base'] = [
      'request' => $request,  // base path for all routes (subfolder of document root)
      'static' => $_SERVER['DOCUMENT_ROOT'] .'/static' .$request,  // path to rendered static site (Hugo public/ folder)
      'system' => $sysDir,  // path to Nette system folder
    ];

    $this->setGroupRequest();
  }

  // run presenter instance and exit if truly presented
  public function run(string $presenter)
  {
    PresenterFactory::create($presenter, $this->attrib);
  }

  // calculate template file name based on the URL
  // return = file exists
  private function template(): bool
  {
    $this->attrib['template'] = $this->$this->attrib['request'][0] .'/index.html';
    return is_file($this->attrib['base']['static'] .$this->attrib['template']);
  }

  // checking http request method
  public function checkMethod(string $method): bool
  {
    return ($this->httpMethod == $method);
  }

  // simple pattern matching test - no variable parts
  public function exactMatch(string $pattern): bool
  {
    return (strpos($this->attrib['request'][0], $pattern) === 0);
  }

  // check regular expression pattern matching
  // replace page params according to the pattern
  public function regMatch(string $pattern): bool
  {
    $pattern = '@^' .$pattern .'$@';
    if (preg_match($pattern, $this->attrib['request'][0], $params) === 1) {
      $this->attrib['request'] = $params;
      return true;
    }
    return false;
  }

  // calculate requestBase for a group and request URI variables
  public function setGroupRequest(string $groupBase = '')
  {
    if ($groupBase) {
      $this->attrib['base']['request'] .= $groupBase; // base URL set for current route group
    }

    $path = substr($this->requestPath .'/', strlen($this->attrib['base']['request']));
    $this->attrib['request'] = explode('/', $path);
    $this->attrib['request'][0] = $path;
  }

}
