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

	// render declared template using Presenter object
	public function render(&$model)
	{
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);

		bdump($model, 'model');
		$latte->render($this->template, $model);
	}

}
