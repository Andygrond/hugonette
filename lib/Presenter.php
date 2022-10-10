<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method returns false (means: route not relevant), next route will be checked
 * @author Andygrond 2022
**/

use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;

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
      if (!is_array($model)) {
        throw new \TypeError(get_class($this) ."::$method has returned " .gettype($model) .". Allowed: array or false.");
      }

      // dump Env if Tracy and development mode
      if (Env::get('mode') == 'development' && Log::$debugMode == 'tracy') {
        bdump(Env::get(), 'Env');
      }

      if ($callback = Env::get('afterLifeCallback')) {
        $finish = new FinishResponse;
      }

      $view = Env::get('namespace.view') .ucfirst(Env::get('view')) .'View';
      new $view($model + $this->model);

      if ($callback) {
        $finish->finish();
        Env::set('afterLife', true);
        $callback($model);
      }

      Log::close(); // effective only when set previously
      exit;
    }
  }

}
