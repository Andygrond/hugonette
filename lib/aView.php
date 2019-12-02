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
		'modelDir' => LIB_DIR .'app/model/',
		'modelNamespace' => '\\App\\Model\\',
	];

	public function __construct($template)
	{
		$this->template = $template;
		bdump($template, 'hugo template');
	}

	// render the previously declared template
	// @$page identifies source of page model data
    // extended classes must define this method
    abstract public function render($page);
	
	// resolve symbolic model notation
	// return instantiated model object
	final public function pageSource($symbol)
	{
		require $this->cfg['modelDir'] .$symbol .'.php';
		$className = $this->cfg['modelNamespace'] .$symbol;
		return new $className();
	}

}
