<?php

namespace Andygrond\Hugonette\Views;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Andygrond\Hugonette\Env;

class PlainView implements ViewInterface
{

  // render model data using plain old PHP template
  public function __construct(array $_model)
  {
    extract($_model);
    unset($_model);
    include(Env::get('base.template') .(Env::get('template')?? '/index.html'));
  }

}
