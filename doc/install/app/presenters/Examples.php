<?php

namespace App\Presenters;

/* Lumen design examples
* @author Andygrond 2020
**/

use Andygrond\Hugonette\Presenter;

class Examples extends Presenter
{

  protected function default()
  {
    return [
      'hello' => 'Hello world',
    ];
  }

}
