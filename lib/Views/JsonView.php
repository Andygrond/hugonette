<?php

namespace Andygrond\Hugonette\Views;

/* MVP JSON View strategy rendering for Hugonette
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Env;

class JsonView implements ViewInterface
{

  // echo model data as JSON object
  public function __construct($model)
  {
    header('Cache-Control: no-cache');
    if (Env::get('mode') != 'development') {  // make possible trace actions
      header('Content-Type: application/json');
    }
    echo json_encode($model, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  }

}
