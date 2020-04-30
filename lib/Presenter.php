<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * methods of Presenter extension class will return an array of model data
 * when Presenter method will return false, next route will be checked
 * @author Andygrond 2020
**/

class Presenter
{
  protected $page;  // page object attributes (can be altered in Presenter)
  protected $view;  // View strategy execution object
  protected $model; // model collected by Presenter for view

  // @page = object of page attributes
  public function __construct(\stdClass $page)
  {
    $this->page = $page;
  }

  // return view object according to view strategy defined in Route
  // this method can be replaced to reflect more sophisticated logic based on $page attribs
  protected function viewStrategy()
  {
    $view = ucfirst($this->page->view) .'View';
    return new $view($this->page);
  }

  // view model data using presenter class@method declared in router
  // @method = presenter method name determined in route definition
  public function run(string $method)
  {
    $this->viewStrategy()->view($this->$method());
  }

}
