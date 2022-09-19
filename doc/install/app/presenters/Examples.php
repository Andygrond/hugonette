<?php

namespace App\Presenters;

/** Hugonette presenter example
* @author Andygrond 2022
*/

use Andygrond\Hugonette\Presenter;
use Andygrond\Hugonette\Env;

class Examples extends Presenter
{

  protected function default()
  {
    return [
      'hello' => 'Hello world',
    ];
  }

  protected function login()
  {
    if (isset($_SESSION['user'])) {
      Env::set('view', 'redirect');
      return [
        'url' => Env::get('base.uri') .'/';
      ]
    }

    return [
      'title' => 'Please log in or register',
    ];
  }
}
