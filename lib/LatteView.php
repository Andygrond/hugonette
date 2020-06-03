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
    if (Log::$debugMode && Log::$channel != 'plain') {
      bdump($page, 'page');
      bdump($model, 'model');
    }

    $latte = new Engine;
    if ($page->base['system']) {
      $latte->setTempDirectory($page->base['system'] .'/temp/latte');
    }

    $template = $page->base['static'] .$page->template;
    $latte->render($template, $model);

    exit;
  }

}
