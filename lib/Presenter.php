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

  // @page = object of page attributes
  public function __construct(\stdClass $page)
  {
    $this->page = $page;
  }

  // return view object according to view strategy defined in Route
  // this method can be replaced to reflect more sophisticated logic based on $page attribs
  protected function viewStrategy()
  {
    $viewClass = '\\Andygrond\\Hugonette\\' .ucfirst($this->page->view) .'View';
    return new $viewClass($this->page);
  }

  // view model data calculated by presenter class@method declared in router
  // @method = presenter method name determined in route definition
  public function run(string $method)
  {
    if (false !== $model = $this->$method()) {
      $this->viewStrategy()->view($this->model + $model);
    }
  }

}
