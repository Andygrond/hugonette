<?php

namespace Andygrond\Hugonette;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

class PlainView implements View
{

  // render model data using plain old PHP template
  public function view(array $model, \stdClass $page)
  {
    if ($model !== false) {

      extract($model);
      $model = null;
      include($page->staticBase .$page->template);

      exit;
    }
  }

}
