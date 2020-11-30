<?php

namespace Andygrond\Hugonette\Views;

/* MVP JSON View strategy rendering for Hugonette
 * @author Andygrond 2020
**/

class JsonView implements ViewInterface
{

  // echo model data as JSON object
  public function __construct(array $model)
  {
    header('Cache-Control: no-cache');
    header('Content-Type: application/json');
    echo json_encode($model, JSON_UNESCAPED_UNICODE);
  }

}
