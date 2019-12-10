<?php

namespace Andygrond\Hugonette;

/* Page rendering with Latte templating engine
 * @author Andygrond 2019
**/

class LatteView extends aView
{

	// configuration data
	private $cfg = [
		'cacheLatte' => TEMP_DIR .'latte',
		'errorTemplate' => ERROR_PAGE,
	];

	// render declared template using $model class
	public function render($page)
	{
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);
		
		if (!$this->template) {
			$this->template = $this->cfg['errorTemplate'];
			$page->status(404);
		}

		$latte->render($this->template, $page->getModel());
	}

}
