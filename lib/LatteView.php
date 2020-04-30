<?php

namespace Andygrond\Hugonette;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;
use stdClass;

class LatteView
{
  protected $template;
//  protected $cacheLatte;

  public function __construct(stdClass $page)
  {
    $this->template = $page->staticBase .$page->template;
//    $this->cacheLatte = $cacheLatte;
  }

  // render model data using Latte templating engine
  public function view(array $model)
  {
    if ($model === false) {
      return;
    }

    $latte = new Engine;
//    $latte->setTempDirectory($this->cacheLatte);
    $latte->render($this->template, $model);

    exit;
  }

}
