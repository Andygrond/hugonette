<?php

namespace Andygrond\Hugonette;

/* JSON API Provider class for Hugonette
 * methods of Provider extension class will return an array of model data
 * @author Andygrond 2020
**/

// use Andygrond\Hugonette\Views\JsonView;

class Provider
{

  protected $page;  // page object attributes

  public function __construct(array $page)
  {
    $this->page = (object) $page;
  }

  // view model data calculated by presenter class@method declared in router
  // @method = presenter method name determined in route definition
  // @page = object of page attributes
  final public function run(string $method)
  {
    $viewClass = $this->page->namespace['view'] .'JsonView';
    (new $viewClass)->view($this->$method());
    Log::close(); // effective only when set previously
    exit;
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
    Log::info('API status', $this->page->status);

    return [
      'status' => $this->page->status,
      'data' => @$data,
    ];
  }

  protected function setStatus($code, $message)
  {
    if ($code >= 400) {
      http_response_code($code);
    }
    $this->page->status = [
      'code' => $code,
      'message' => $message,
    ];
  }
}
