<?php

namespace Andygrond\Hugonette;

/* MVP View rendering for Hugonette
* @author Andygrond 2020
**/

class View
{
  protected $template;
  protected $cacheLatte;

  public function __construct(string $template, string $cacheLatte)
  {
    $this->template = $template;
    $this->cacheLatte = $cacheLatte;
  }

  // render model data using Latte templating engine
  public function latte(array &$_model)
  {
    $latte = new \Latte\Engine;
    $latte->setTempDirectory($this->cacheLatte);

    $latte->render($this->template, $_model);
  }

  // render model data using plain old PHP template
  public function plain(array &$_model)
  {
    extract($_model);
    $_model = null;
    include($this->template);
  }

  // send model data as JSON object
  public function json(array &$_model)
  {
    header('Content-Type: application/json');
    echo json_encode($_model, JSON_UNESCAPED_UNICODE);
  }

}
