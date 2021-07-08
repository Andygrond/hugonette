<?php

namespace Andygrond\Hugonette\Views;

/** MVP Blind View strategy rendering for Hugonette
  * Do absolutely nothing because view is delivered independently
  * Tested with Box\Spout openToBrowser method
  * @author Andygrond 2021
  */

class BlindView implements ViewInterface
{

  // do nothing
  public function __construct(array $model)
  {
  }

}
