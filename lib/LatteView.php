<?php

namespace Andygrond\Hugonette;

/* MVP Latte View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;

class LatteView implements View
{
  private $page;

  public function __construct(\stdClass $page)
  {
    $this->page = $page;
  }

  // render model data using Latte templating engine
  public function view(array $model)
  {
    if (Log::$debugMode && Log::$channel != 'plain') {
      bdump($this->page, 'page');
      bdump($model, 'model');
    }

    $latte = new Engine;
    if ($this->page->base['system']) {
      $latte->setTempDirectory($this->page->base['system'] .'/temp/latte');
    }

    $template = $this->page->base['static'] .$this->page->template;
    $latte->render($template, $model);

    exit;
  }

}
