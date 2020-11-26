<?php

namespace Andygrond\Hugonette;

/* Page state calculations for Hugonette
 * @author Andygrond 2020
**/

class Page
{
  private $trace = [];  // trace of matched routes

  public function __construct()
  {

  }

  // run presenter instance and exit if truly presented
  public function run(string $presenter)
  {
    // keep trace of matched routes for the request
    $lineNo = debug_backtrace()[1]['line'];
    $this->trace[$lineNo] = $presenter;
    Env::set('trace', $this->trace);

    // call Presenter
    PresenterFactory::create($presenter, Env::get());
  }

}
