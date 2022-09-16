<?php

namespace App\Presenters;

/** Hugonette design examples
* @author Andygrond 2022
*/

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
