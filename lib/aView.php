<?php

namespace Andygrond\Hugonette;

/* Abstract View class for Hugonette
 * @author Andygrond 2019
**/

abstract class aView
{
	protected $template;
	protected $params;
		
	public function __construct($template, $params)
	{
		$this->template = $template;
		$this->params = $params;
		
		bdump($template, 'template');
	}

	// render previously declared template
	// @$page identifies source of page model data
    // extended classes must define this method
    abstract public function render($page);
	
}
