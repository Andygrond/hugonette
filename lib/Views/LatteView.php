<?php

namespace Andygrond\Hugonette\Views;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;
use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;

class LatteView implements ViewInterface
{

  // render model data using Latte templating engine
  public function __construct(array $model)
  {
    // dump Env if Log uses native Logger and Tracy in debug mode
    if (@Log::$debug == 'dev') {  // TODO modernize
      bdump(Env::get(), 'Env');
      bdump($model, 'Model');
    }

    $latte = new Engine;
    $latte->setTempDirectory(Env::get('base.system') .'/temp/latte');
    $template = Env::get('base.template') .(Env::get('template')?? '/index.html');
    $latte->render($template, $model);
  }

}
