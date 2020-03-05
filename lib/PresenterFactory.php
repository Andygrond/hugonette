<?php

namespace Andygrond\Hugonette;

/* MVP Presenter Factory for Hugonette
* @author Andygrond 2020
**/

final class PresenterFactory
{

  // return instantiated presenter object
  public static function create(string $presenter, string $namespace): Presenter
  {
    [ $class, $method ] = explode(':', $presenter .':default');
    $class = $namespace .'\\' .ucwords($class);

    return new $class($method);
  }

}
