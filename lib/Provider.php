<?php

namespace Andygrond\Hugonette;

/* JSON API Provider class for Hugonette
 * methods of Provider extension class will return an array of model data
 * @author Andygrond 2020
**/

class Provider
{

  public function __construct()
  {
  }

  /** view model data calculated by presenter class:method declared in router
  * @param method = presenter method name determined in route definition
  */
  final public function run(string $method)
  {
    $viewClass = Env::get('namespace.view') .'JsonView';
    $model = $this->$method();
    
    if (false !== $model) {
      if (is_array($model)) {
        (new $viewClass)->view($model);
        Log::close(); // effective only when set previously
        exit;
      } else {
        throw new \TypeError("Please return array or false in " .get_class($this) ."->$method()");
      }
    }
  }

  // Provider default class called in Route
  protected function default()
  {
    try {
      if ($this->validRequest()) {
        $data = $this->getModel();
        $data? $this->setStatus(200, 'OK') : $this->setStatus(204, 'No Content');
      } else {
        $this->setStatus(406, 'Unknown entity requested');
      }
    } catch (\Throwable $t) {
       $this->setStatus(500, $t->getMessage());
    }
    Log::info('API status', Env::get('status'));

    return [
      'status' => Env::get('status'),
      'data' => @$data,
    ];
  }

  protected function setStatus($code, $message)
  {
    if ($code >= 400) {
      http_response_code($code);
    }
    Env::set('status', [
      'code' => $code,
      'message' => $message,
    ]);
  }
}
