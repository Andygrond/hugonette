<?php

namespace Andygrond\Hugonette;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use stdClass;

class PlainView
{
  protected $template;

  public function __construct(stdClass $page)
  {
    $this->template = $page->staticBase .$page->template;
//    $this->cacheLatte = $cacheLatte;
  }

  // render model data using plain old PHP template
  public function view(array $model)
  {
    if ($model === false) {
      return;
    }

    extract($model);
    $model = null;
    include($this->template);

    exit;
  }

}
