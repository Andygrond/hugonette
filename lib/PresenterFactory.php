<?php

namespace Andygrond\Hugonette;

/* MVP Presenter Factory for Hugonette
* @author Andygrond 2020
**/

final class PresenterFactory
{

  /**
  * @return - instantiated presenter or provider object
  */
  public static function create(string $presenter)
  {
    [ $class, $method ] = explode(':', $presenter .':default');
    $class = Env::get('namespace.presenter') .ucwords($class);

    (new $class)->run($method);
  }

}
