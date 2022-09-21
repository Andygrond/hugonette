<?php

namespace Andygrond\Hugonette\Views;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;
use Andygrond\Hugonette\Env;

class LatteView implements ViewInterface
{

  // render model data using Latte templating engine
  public function __construct(array $model)
  {
    // render in Latte
    $latte = new Engine;

    $filters = Env::get('base.system') .'/app/filters.php';
    if (file_exists($filters)) {
      include($filters);
    }

    $latte->setTempDirectory(Env::get('base.system') .'/temp/latte');
    $template = Env::get('base.template') .(Env::get('template')?? '/index.html');
    $latte->render($template, $model);
  }

}
