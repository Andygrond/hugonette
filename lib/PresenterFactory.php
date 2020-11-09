<?php

namespace Andygrond\Hugonette;

/* MVP Presenter Factory for Hugonette
* @author Andygrond 2020
**/

final class PresenterFactory
{

  // return instantiated presenter or provider object
  public static function create(string $presenter, $page)
  {
    [ $class, $method ] = explode(':', $presenter .':default');
    $class = $page['namespace']['presenter'] .ucwords($class);

    (new $class($page))->run($method);
  }

}
