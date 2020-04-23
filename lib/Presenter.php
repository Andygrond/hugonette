<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * @author Andygrond 2020
**/

class Presenter
{
  private $method;        // presenter method determined in route

  protected $page;      // navigation data for response processing
  protected $template;  // template set in presenter extension
  protected $view;      // view type set in presenter extension

  public function __construct(string $method = 'default')
  {
    $this->method = $method;
  }

  // return model data using presenter class@method declared in router
  // method of extended presenter class will return an array of model data
  // when the route appears not relevant - presenter method will return false
  public function run(\stdClass $page)
  {
    $this->page = $page;

    $model = $this->{$this->method}();	// presenter method call

    if ($model !== false) { // only when passed by presenter
      if (Log::$debug) {
        bdump($this->page, 'page');
        bdump($model, 'model');
      }

      $view = $this->view ?: $page->view;
      $template = $this->template ?? $page->template;
      $template = ($view == 'json')? '' : $page->publishBase .$template;
      (new View($template, $page->cacheLatte))->$view($model);

      exit;
    }
    // else bypass to the next route
  }

  // data or file transfer
  public function file(\stdClass $page)
  {
    $this->page = $page;

    $model = $this->{$this->method}();	// presenter method call

    (new Upload($page))->file($model);
  }

  // redirect @$to if URI simply starts from $pattern or $pattern is empty
  // @$permanent in Presenter defaults to http code 302 Found
  public function redirect(string $to, bool $permanent = false)
  {
    $code = $permanent? 301 : 302;
    Log::info($code .' Redirected to: ' .$to);
    header('Location: ' .$to, true, $code);

    exit;
  }

}
