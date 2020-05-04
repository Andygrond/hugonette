<?php

namespace Andygrond\Hugonette;

/* MVP View interface for Hugonette
* @author Andygrond 2020
**/

interface View
{

  public function view(array $model, \stdClass $page);

}
