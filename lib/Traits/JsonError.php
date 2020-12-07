<?php

namespace Andygrond\Hugonette\Traits;

/* JSON Error human friendly
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Log;

trait JsonError
{
  // get the last json error
  public function jsonError()
  {
    switch (json_last_error()) {
      case JSON_ERROR_NONE:
        return 'No errors';
      case JSON_ERROR_DEPTH:
        return 'Maximum stack depth exceeded';
      case JSON_ERROR_STATE_MISMATCH:
        return 'Underflow or the modes mismatch';
      case JSON_ERROR_CTRL_CHAR:
        return 'Unexpected control character found';
      case JSON_ERROR_SYNTAX:
        return 'Syntax error';
      case JSON_ERROR_UTF8:
        return 'Invalid UTF-8 characters';
      default:
        return 'Unknown JSON decoding error';
    }
  }

}
