<?php

namespace Andygrond\Hugonette;

/* Page rendering with Latte templating engine
 * @author Andygrond 2019
**/

class LatteView extends aView
{

	// configuration data
	private $cfg = [
		'cacheLatte' => TEMP_DIR .'/latte',
	];

	// render declared template using $model class
	public function render($model)
	{
		$csrf = new CsrfSession();

	// use data model class
		$page = $this->pageSource($model);

	// use latte template engine
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);
		$latte->render($this->template, $page->getModel());
	}

}
