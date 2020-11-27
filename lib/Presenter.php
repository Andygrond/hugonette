<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method is empty, next route will be checked
 * @author Andygrond 2020
**/

class Presenter
{
  protected $model = []; // base model data

  public function __construct()
  {
  }

  /**
  * @return: view object according to view strategy defined in Route
  */
  final protected function viewStrategy()
  {
    $viewClass = Env::get('namespace.view') .ucfirst(Env::get('view')) .'View';
    return new $viewClass();
  }

  /** view model data calculated by presenter class:method declared in router
  * @param method = presenter method name determined in route definition
  */
  final public function run(string $method)
  {
    $model = $this->$method();
    if (false !== $model) {
      if (is_array($model)) {
        $this->viewStrategy()->view($this->model + $model);
        Log::close(); // effective only when set previously
        exit;
      } else {
        throw new \TypeError("Please return array or false in " .get_class($this) ."->$method()");
      }
    }
  }

}
