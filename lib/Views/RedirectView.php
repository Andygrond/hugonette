<?php

namespace Andygrond\Hugonette\Views;

/* MVP Redirect View strategy rendering for Hugonette
* @author Andygrond 2020
**/

use Andygrond\Hugonette\Log;

class RedirectView implements View
{
  public function __construct(\stdClass $page = null)
  {
  }

  // redirect to another URL
  // @$model['url']
  // @$model['permanent'] true = default = 301 Moved Permanently; false = 302 Found
  public function view(array $model)
  {
    $to = $model['url'];
    $code = (@$model['permanent'] !== false)? 301 : 302;
    Log::info($code .' Redirected to: ' .$to);
    header('Location: ' .$to, true, $code);
  }

}
