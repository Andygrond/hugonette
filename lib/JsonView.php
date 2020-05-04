<?php

namespace Andygrond\Hugonette;

/* MVP JSON View strategy rendering for Hugonette
* @author Andygrond 2020
**/

class JsonView implements View
{

  // send model data as JSON object
  public function view(array $model, \stdClass $page)
  {
    if ($model !== false) {

      header('Cache-Control: no-cache');
      header('Content-Type: application/json');
      echo json_encode($model, JSON_UNESCAPED_UNICODE);

      exit;
    }
  }

}
