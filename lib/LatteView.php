<?php

namespace Andygrond\Hugonette;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;

class LatteView implements View
{

  // render model data using Latte templating engine
  public function view(array $model, \stdClass $page)
  {
    if ($model !== false) {
      bdump($model);

      $template = $page->staticBase .$page->template;
      $latte = new Engine;
      if ($page->tempDir) {
        $latte->setTempDirectory($page->tempDir .'/latte');
      }
      $latte->render($template, $model);

      exit;
    }
  }

}
