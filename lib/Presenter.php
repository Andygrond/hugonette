<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method is empty, next route will be checked
 * @author Andygrond 2020
**/

class Presenter
{
  protected $page;  // page object attributes (can be altered in Presenter)
  protected $model = []; // base model data

  public function __construct()
  {
    $this->page = (object) Env::get();
  }

  /**
  * @return: view object according to view strategy defined in Route
  */
  final protected function viewStrategy()
  {
    $viewClass = Env::get('namespace.view') .ucfirst(Env::get('view')) .'View';
    return new $viewClass($this->page);
  }

  /** view model data calculated by presenter class:method declared in router
  * @param method = presenter method name determined in route definition
  */
  final public function run(string $method)
  {
    if (false !== $model = $this->$method()) {
      $this->viewStrategy()->view($this->model + $model);
      Log::close(); // effective only when set previously
      exit;
    }
  }

}
