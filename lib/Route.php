<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugonette
 * @author Andygrond 2020
**/

class Route
{
  private $page;  // page object
  private $allowedMethods = ['get', 'post', 'put', 'delete'];

  // $sysDir - path to Nette system if exists
  public function __construct(string $sysDir = null)
  {
    $this->page = new Page($sysDir);
  }

  // route for single request method
  // @method - http method as a route function name
  // @args = [$pattern, $model]
  public function __call(string $method, array $args)
  {
    if ($this->page->checkMethod($method)) {
      if ($this->page->regMatch($args[0])) {
        $this->page->run($args[1]);
      }
    } elseif (!in_array($method, $this->allowedMethods)) {
      throw new \BadMethodCallException("Router method not found: $method");
    }
  }

  // full static GET with one common presenter (runs all static pages at once)
  public function pages(string $presenter)
  {
    if ($this->page->checkMethod('get') && $this->page->template()) {
      $this->page->run($presenter);
    }
  }

  // if applicable, should be placed as the last routing directive in a group - for not routed URI
  // @presenter usually shows status 404 with the native navigation panels
  public function notFound(string $presenter)
  {
    $this->page->run($presenter);
  }

  // redirect @$to if URI simply starts from $pattern or $pattern is empty
  // this can be used as the last routing directive in group or freely
  // @$permanent in Route defaults to http code 301 Moved Permanently
  // @$permanent set to false = doesn't inform search engines about the change
  public function redirect(string $pattern, string $url, bool $permanent = true)
  {
    if (!$pattern || $this->page->exactMatch($pattern)) {
      (new RedirectView())->view([
        'url' => $url,
        'permanent' => $permanent,
      ]);
    }
  }

  // register a set of routes with shared attributes.
  public function group(string $pattern, \Closure $callback, array $attrib = [])
  {
    if (!$pattern || $this->page->exactMatch($pattern)) {
      $this->page->setGroupRequest($pattern);

      $parentAttrib = $this->page->attrib;  // it's the only place you can use it directly
      $this->attributes($attrib);
      call_user_func($callback, $this);
      $this->page->attrib = $parentAttrib;  // it's the only place you can use it directly
    }
  }

  // update given page attributes
  public function attributes($attrib)
  {
    $this->page->attrib = $attrib + $this->page->attrib;
  }

}
