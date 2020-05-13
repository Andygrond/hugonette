<?php

namespace Andygrond\Hugonette;

/* MVP Redirect View strategy rendering for Hugonette
* @author Andygrond 2020
**/

class RedirectView implements View
{

  // redirect to another URL
  // @$model['url']
  // @$model['permanent'] true = default = 301 Moved Permanently; false = 302 Found
  public function view(array $model, \stdClass $page = null)
  {
    if ($model === false)
      return;

    $to = $model['url'];
    $code = (@$model['permanent'] !== false)? 301 : 302;
    Log::info($code .' Redirected to: ' .$to);
    header('Location: ' .$to, true, $code);

    exit;
  }

}
