<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method returns false (means: route not relevant), next route will be checked
 * @author Andygrond 2023
**/

use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;

abstract class Presenter
{
  protected $model = []; // base model data - can be set separately

  /** calculate Model data using Presenter class:method and pass it to View
  * @param $method = presenter method name determined in route definition
  */
  final public function run(string $method)
  {
    $model = $this->$method();
    $view = ucfirst(Env::get('view'));
    $viewClass = Env::get('namespace.view') .$view .'View';
    $lastRoute = false;

    if ($model === false) {
      if (isset($this->model['status'])) { // error to be presented
        Env::set('template', Env::get('hidden.file.error'));
        new $viewClass($this->model);
        $lastRoute = true;
      }

    } else {
      if (!is_array($model)) {
        throw new \TypeError(get_class($this) ."::$method has returned " .gettype($model) .". Allowed: array or false.");
      }

      if ($callback = Env::get('afterLifeCallback')) {
        $finish = new FinishResponse;
      }

      new $viewClass($model + $this->model);
      $lastRoute = true;

      if ($callback) {
        $finish->finish();
        Env::set('afterLife', true);
        $callback($model);
      }
    }

    if ($lastRoute) { // this route is the last one
      // dump Env if Tracy and development mode
      if (Env::get('mode') == 'development' && Log::$debugMode == 'tracy') {
        bdump(Env::get(), 'Env');
      }

      Log::close(); // effective only when set previously
      exit;
    }

  }

}
