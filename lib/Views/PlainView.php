<?php

namespace Andygrond\Hugonette\Views;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Andygrond\Hugonette\Env;

class PlainView implements View
{
  private $template;

  public function __construct()
  {
    $this->template = Env::get('base.template') .(Env::get('template')?? '/index.html');
  }

  // render model data using plain old PHP template
  public function view(array $_model)
  {
    extract($_model);
    unset($_model);
    include($this->template);
  }

}
