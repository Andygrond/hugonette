<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugonette
 * @author Andygrond 2020
**/

class Route
{
  private $pageObject;  // page object
  private $allowedMethods = ['get', 'post', 'put', 'delete'];
  private $httpMethod;  // http method lowercase

  // $sysDir - path to Nette system if exists
  public function __construct(string $sysDir = null, array $attrib = [])
  {
    $this->pageObject = new Page($sysDir);
    $this->httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
    $this->attributes($attrib);
  }

  // route for single request method
  // @method - http method as a route function name
  // @args = [$pattern, $model]
  public function __call(string $method, array $args)
  {
    if ($this->httpMethod == $method) {
      if ($this->regMatch($args[0])) {
//        $this->pageObject->attrib['request'] = $params;
        $this->pageObject->run($args[1]);
      }
    } elseif (!in_array($method, $this->allowedMethods)) {
      Log::trigger("Router method not found: $method");
    }
  }

  // full static GET with one common presenter (runs all static pages at once)
  public function static(string $presenter)
  {
    if ($this->httpMethod == 'get' && $this->pageObject->template()) {
      $this->pageObject->run($presenter);
    }
  }

  // if applicable, should be placed as the last routing directive in a group - for not routed URI
  // @presenter usually shows status 404 with the native navigation panels
  public function notFound(string $presenter)
  {
    $this->pageObject->run($presenter);
  }

  // redirect @$to if URI simply starts from $pattern or $pattern is empty
  // this can be used as the last routing directive in group or freely
  // @$permanent in Route defaults to http code 301 Moved Permanently
  // @$permanent set to false = doesn't inform search engines about the change
  public function redirect(string $pattern, string $url, bool $permanent = true)
  {
    if (!$pattern || $this->exactMatch($pattern)) {
      (new RedirectView())->view([
        'url' => $url,
        'permanent' => $permanent,
      ]);
    }
  }

  // register a set of routes with shared attributes.
  public function group(string $pattern, \Closure $callback, array $attrib = [])
  {
    if (!$pattern || $this->exactMatch($pattern)) {
      $parentAttrib = $this->pageObject->attrib;

      $this->pageObject->setGroupRequest($pattern);
      $this->attributes($attrib);
      call_user_func($callback, $this);

      $this->pageObject->attrib = $parentAttrib;
    }
  }

  // update given page attributes
  public function page($attrib)
  {
    $this->pageObject->attrib = $attrib + $this->pageObject->attrib;
  }

  // simple pattern matching test - no variable parts
  private function exactMatch(string $pattern): bool
  {
    return (strpos($this->pageObject->attrib['request']['item'], $pattern) === 0);
  }

  // check regular expression pattern matching
  // replace page params according to the pattern
  private function regMatch(string $pattern): bool
  {
    $pattern = '@^' .$pattern .'$@';
    return preg_match($pattern, $this->pageObject->attrib['request']['item']);
  }

}
