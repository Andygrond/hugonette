<?php

namespace Andygrond\Hugonette;

/* Page rendering with plain old PHP template
 * @author Andygrond 2019
**/

class PlainView extends aView
{
	// render declared template using $model class
	public function render($model)
	{
	// use data model class
		$page = $this->pageSource($model);
		extract($page->run());
	
	// use native php template
		include($this->template);
	}
	
}
