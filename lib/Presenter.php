<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method returns false (means: route not relevant), next route will be checked
 * @author Andygrond 2020
**/

abstract class Presenter
{
  protected $model = []; // base model data - can be set separately

  /** calculate Model data using Presenter class:method and pass it to View
  * @param method = presenter method name determined in route definition
  */
  final public function run(string $method)
  {
    $model = $this->$method();

    if (false !== $model) {
      if (is_array($model)) {
        $view = Env::get('namespace.view') .ucfirst(Env::get('view')) .'View';
        new $view($model + $this->model);
        Log::close(); // effective only when set previously
        exit;

      } else {
        throw new \TypeError(get_class($this) ."::$method has returned " .gettype($model) .". Allowed: array or false.");
      }
    }
  }

}
