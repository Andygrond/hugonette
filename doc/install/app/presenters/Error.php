<?php

namespace App\Presenters;

/** Hugonette error model
* @author Andygrond 2022
*/

use Andygrond\Hugonette\Presenter;
use Andygrond\Hugonette\Helpers\Status;

class Error extends Presenter
{
  public function __call($code, $args)
  {
    $status = new Status($code, 'pl');
    if ($message = $status->message()) {
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
