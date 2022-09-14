<?php

namespace App\Presenters;

/* Lumen homepage model
* @author Andygrond 2020
**/

use App\Library\Status;

class Error extends LumenPresenter
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
      'warnings' => [],
    ];
  }

}
