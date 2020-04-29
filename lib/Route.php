<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugonette
 * @author Andygrond 2020
**/

class Route
{
  private $page;  // page object
  private $allowedMethods = ['get', 'post', 'put', 'delete'];

  // @$publishBase path to static pages (e.g. hugo public folder)
  public function __construct(array $attributes)
  {
    $this->page = new Page($attributes);
  }

  // Log shutdown needed to write to file
  public function __destruct()
  {
    Log::close(); // effective only when set
  }

  // route for single request method
  // @method - http method as a route function name
  // @args = [$pattern, $model]
  public function __call(string $method, array $args)
  {
    $this->page->trialCounter++;
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
    $this->page->trialCounter++;
    if ($this->page->checkMethod('get') && $this->page->template()) {
      $this->page->run($presenter);
    }
  }

  // if applicable, should be placed as the last routing directive in a group - for not routed URI
  // @presenter usually shows status 404 with the native navigation panels
  public function notFound(string $presenter)
  {
    $this->page->trialCounter++;
    $this->page->run($presenter);
  }

  // redirect @$to if URI simply starts from $pattern or $pattern is empty
  // this can be used as the last routing directive in group or freely
  // @$permanent in Route defaults to http code 301 Moved Permanently
  // @$permanent set to false = doesn't inform search engines about the change
  public function redirect(string $pattern, string $to, bool $permanent = true)
  {
    $this->page->trialCounter++;
    if (!$pattern || $this->page->exactMatch($pattern)) {
      $this->page->redirect($to, $permanent);
    }
  }

  // register a set of routes with shared attributes.
  public function group(string $pattern, \Closure $callback, array $attributes = [])
  {
    if ($this->page->exactMatch($pattern)) {
      $parentAttributes = $this->page->updateAttributes($pattern, $attributes);
      call_user_func($callback, $this);
      $this->page->refreshAttributes($parentAttributes);
    }
  }

}
