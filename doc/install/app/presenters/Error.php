<?php

namespace App\Presenters;

/** Hugonette error model
* @author Andygrond 2022
*/

use Andygrond\Hugonette\Presenter;
use App\Library\Status;

class Error extends Presenter
{
  public function __call($code, $args)
  {
    if ($message = Status::message($code, 'pl')) {
      http_response_code($code);
    }

    return [
      'title' => 'You are not supposed to be here...',
      'status' => [
        'code' => $code,
        'message' => $message,
      ],
      'args' => $args,
    ];
  }

}
