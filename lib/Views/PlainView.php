<?php

namespace Andygrond\Hugonette\Views;

/* MVP Plain View strategy rendering for Hugonette
* @author Andygrond 2020
**/

class PlainView implements View
{
  private $template;

  public function __construct(\stdClass $page)
  {
    $this->template = $page->base['static'] .$page->base['template'] .($page->template?? '/index.html');
  }

  // render model data using plain old PHP template
  public function view(array $_model)
  {
    extract($_model);
    unset($_model);
    include($this->template);
  }

}