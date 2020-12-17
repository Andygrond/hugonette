<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugonette
 * @author Andygrond 2020
**/

class Route
{
  private $allowedMethods = ['get', 'post', 'put', 'delete'];
  private $httpMethod; // http method lowercase

  /**
  * @param sysDir - path to framework (i.e. Nette system)
  */
  public function __construct()
  {
    // set Env request
    [ $path ] = explode('?', urldecode($_SERVER['REQUEST_URI']));
    $isHtml = (substr($path, -5) == '.html');
    $path = $isHtml? substr($path, strlen($uriBase), -5) : substr(rtrim($path, '/'), strlen(Env::get('base.uri'))) .'/';
// zrobiłem rozróżnienie: gdy jest to katalog ma slash na końcu - plik nie ma - czy to jest potrzebne?
    Env::set('request', [
      'group' => '',    // router group base
      'item' => $path,  // request details (path in group)
      'segments' => explode('/', trim($path, '/')),
    ]);

    Env::set('trace', []);  // trace must be an array
    $this->httpMethod = strtolower($_SERVER['REQUEST_METHOD']);
  }

  /** route for single request method
  * @param method - http method as a route function name
  * @param args = [$pattern, $model]
  */
  public function __call(string $method, array $args)
  {
    if ($this->httpMethod == $method) {
      if ($this->regMatch($args[0])) {
        $this->run($args[1]);
      }
    } elseif (!in_array($method, $this->allowedMethods)) {
      throw new \UnexpectedValueException("Router method not found: $method");
    }
  }

  /** full static GET with one common presenter (runs all static pages at once)
  * @param presenter name as declared in route
  */
  public function static(string $presenter)
  {
    if ($this->httpMethod == 'get' && $this->template()) {
      $this->run($presenter);
    }
  }

  /** if applicable, should be placed as the last routing directive in a group - for not routed URI
  * @param presenter usually shows status 404 with the native navigation panels
  */
  public function notFound(string $presenter)
  {
    $this->run($presenter);
  }

  /** redirect if URI simply starts from $pattern or $pattern is empty
  * this can be used freely, but typically as the last routing directive in group
  * @param pattern string to match
  * @param url addres to redirect to
  * @param permanent in Route defaults to http code 301 Moved Permanently
  * $permanent set to false = doesn't inform search engines about the change
  */
  public function redirect(string $pattern, string $url, bool $permanent = true)
  {
    if (!$pattern || $this->exactMatch($pattern)) {
      (new RedirectView())->view([
        'url' => $url,
        'permanent' => $permanent,
      ]);
    }
  }

  /** run a set of routes with shared attributes
  * @param pattern string to match
  * @param callback function to run when matched
  */
  public function group(string $pattern, \Closure $callback)
  {
    if (!$pattern || $this->exactMatch($pattern)) {
      $parentAttrib = Env::get();

      // calculate request for a group
      Env::append('request.group', $pattern); // base URL for current route group
      Env::set('request.item', substr(Env::get('request.item'), strlen($pattern)));
      // run the closure
      call_user_func($callback, $this);

      Env::restore($parentAttrib);
    }
  }

  /** simple pattern matching test - no variable segments
  * @param pattern string to match
  * @return = matched
  */
  private function exactMatch(string $pattern): bool
  {
    return (strpos(Env::get('request.item'), $pattern) === 0);
  }

  /** check regular expression pattern matching
  * @param pattern string to match
  * @return = matched
  */
  private function regMatch(string $pattern): bool
  {
    return preg_match('@^' .$pattern .'$@', Env::get('request.item'));
  }

  /** run presenter instance and exit if truly presented
  * @param presenter name as declared in route
  */
  private function run(string $presenter)
  {
    // keep trace of matched routes for the request
    Env::append('trace', debug_backtrace()[1]['line'] .': ' .$presenter);
    // call Presenter
    PresenterFactory::create($presenter);
  }

  /** calculate template file name based on the URL
  * @return = file exists
  */
  public function template(): bool  // TODO!!! to nie powinno działac po ostatnich zmianach
  {
//    Env::set('base.template', '');
    $template = Env::get('base.uri') .Env::get('request.item');
    $template .= (substr($template, -1) == '/')? 'index.html' : '.html';

    if (is_file(Env::get('base.static') .$template)) {
      Env::set('template', $template);
      return true;
    }
    return false;
  }

}
