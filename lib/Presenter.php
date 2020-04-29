<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * @author Andygrond 2020
**/

class Presenter
{
  protected $page;      // page attributes
  protected $template;  // template set in presenter extension
  protected $view;      // view type set in presenter extension

  // @page = object of page attributes
  public function __construct(\stdClass $page)
  {
    $this->page = $page;
  }

  // return model data using presenter class@method declared in router
  // method of extended presenter class will return an array of model data
  // when presenter method will return false, the route appears not relevant
  // @method = presenter method determined in route definition
  public function run(string $method)
  {
    $model = $this->$method();	// presenter method call

    if ($model !== false) { // only when passed by presenter
      if (Log::$debug) {
        bdump($this->page, 'page');
        bdump($model, 'model');
      }

      $view = $this->view ?: $page->view;
      $template = $this->template ?? $page->template;
      $template = ($view == 'json')? '' : $page->publishBase .$template;
      (new View($template))->$view($model);

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

}
