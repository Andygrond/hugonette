<?php

namespace Andygrond\Hugonette;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

class PlainView implements View
{

  // render model data using plain old PHP template
  public function view(array $_model, \stdClass $page)
  {
    if ($_model === false)
      return;

    extract($_model);
    unset($_model);
    include($page->base['static'] .$page->template);

    exit;
  }

}
