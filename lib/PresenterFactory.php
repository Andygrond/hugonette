<?php

namespace Andygrond\Hugonette;

/* MVP Presenter Factory for Hugonette
* @author Andygrond 2020
**/

final class PresenterFactory
{

  // return instantiated presenter object
  public static function create(string $presenter, $page)
  {
    [ $class, $method ] = explode(':', $presenter .':default');
    $class = $page['presenterNamespace'] .'\\' .ucwords($class);

    (new $class((object) $page))->run($method);
  }

}
