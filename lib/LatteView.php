<?php

namespace Andygrond\Hugonette;

/* Page rendering
 * Andrzej Grondziowski 2019
**/

class LatteView
{
	private $template;
	private $model;

	// configuration data
	private $cfg = [
		'cacheLatte' => TEMP_DIR .'/latte',
	];

	public function __construct($template)
	{
		$this->template = $template;
		bdump($template, 'hugo template');
	}
	
	// render the page using data model
	public function render($page)
	{
		$csrf = new CSRF_Session();

		$className = '\\Andygrond\\Pages\\' .$page;
		$page = new $className();

	// use latte template engine
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);
		$latte->render($this->template, $model);
	}


}
