<?php

namespace Andygrond\Hugonette;

/* Abstract View class for Hugonette
 * @author Andygrond 2019
**/

abstract class aView
{
	protected $template;
		
	public function __construct($template = false)
	{
		$this->template = $template;
	}

	// render previously declared template
	// @$model data to be published
    // extended classes must define this method
    abstract public function render(&$model);
	
}
