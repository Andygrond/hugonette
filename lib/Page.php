<?php

namespace Andygrond\Hugonette;

/* Page state attributes for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  // page attributes - this can be altered by route group attributes
  private $attrib = [
    'view' => 'plain',    // view mode [ plain | latte | json | upload | redirect ]
    'presenterNamespace' => 'App\Presenters',
  ];

  private $requestPath;

  public function __construct(array $attributes)
  {
    $this->attrib = $attributes + $this->attrib;
    $this->attrib['httpMethod'] = strtolower($_SERVER['REQUEST_METHOD']);

    [ $path ] = explode('?', $_SERVER['REQUEST_URI']);
//    $this->attrib['requestPath'] = rtrim($path, '/');
    $this->requestPath = rtrim($path, '/');

    $this->setGroupRequest();
  }

  // run presenter instance and exit if truly presented
  // private function runPresenter(string $presenter, bool $static = false)
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

  // get initial array of page attributes and update them to group values
  public function updateAttributes(string $pattern, array $attributes): array
  {
    $arch = $this->attrib;
    if (count($attributes)) {
      $this->attrib = $attributes + $arch;
    }
    $this->setGroupRequest($pattern);
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
  private function setGroupRequest(string $groupBase = '')
  {
    if ($groupBase) {
      $this->attrib['base']['request'] .= $groupBase; // base for current route group
    }

    $path = substr($this->requestPath .'/', strlen($this->attrib['base']['request']));
    $this->attrib['request'] = explode('/', $path);
    $this->attrib['request'][0] = $path;
  }

}
