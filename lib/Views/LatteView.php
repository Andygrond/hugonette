<?php

namespace Andygrond\Hugonette\Views;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;
use Andygrond\Hugonette\Log;

class LatteView implements View
{
  private $base;
  private $template;
  private $debug;

  public function __construct(\stdClass $page)
  {
    $this->base = $page->base;
    $this->template = $page->base['static'] .$page->base['template'] .($page->template?? '/index.html');
    $this->debug = (Log::$debugMode && Log::$channel != 'plain'); // Log is set in debug mode and uses Tracy
    if ($this->debug) {
      bdump($page, 'page');
    }
  }

  // render model data using Latte templating engine
  public function view(array $model)
  {
    if ($this->debug) {
      bdump($model, 'model');
    }

    $latte = new Engine;
    if ($this->base['system']) {
      $latte->setTempDirectory($this->base['system'] .'/temp/latte');
    }

    $latte->render($this->template, $model);
  }

}
