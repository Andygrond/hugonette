<?php

namespace Andygrond\Hugonette;

/* Page rendering with Latte templating engine
 * @author Andygrond 2019
**/

class LatteView extends aView
{

	// configuration data
	private $cfg = [
		'cacheLatte' => LIB_DIR .'temp/latte',
	];

	// render declared template using $model class
	public function render($page)
	{
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);

		$latte->render($this->template, $page->getModel($this->params));
	}

}
