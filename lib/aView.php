<?php

namespace Andygrond\Hugonette;

/* Abstract View class for Hugonette
 * @author Andygrond 2019
**/

abstract class aView
{
	protected $template;
		
	// configuration data
	private $cfg = [
		'modelDir' => MODEL_DIR,
		'modelNamespace' => MODEL_NS,
	];

	public function __construct($template)
	{
		$this->template = $template;
		bdump($template, 'hugo template');
	}

	// render the previously declared template
	// @$page identifies source of page model data
    // extending classes must define this method
    abstract public function render($page);
	
	// resolve symbolic model notation
	// return instantiated model object
	// TODO! now only class name is valid
	final public function pageSource($symbol)
	{
		require $this->cfg['modelDir'] .$symbol .'.php';
		$className = $this->cfg['modelNamespace'] .$symbol;
		return new $className();
	}

}
