<?php

namespace Andygrond\Hugonette\Helpers;

/** Get unified fault message
 * @author Andygrond 2023
**/

class Status
{

  private $code;
  private $lang;

  public function __construct($code, $lang = 'en')
  {
    $this->code = $code;
    $this->lang = $lang;
  }

  // get the last json error
  public function get($message = '')
  {
    return [
      'code' => $this->code,
      'message' => $message?: $this->message(),
    ];
  }

  public function message()
  {
    return HttpStatus::code($this->code, $this->lang);
  }

  public function set()
  {
    http_response_code($this->code);
    return $this;
  }

}
