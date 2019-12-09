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
		'errorTemplate' => ERROR_PAGE,
	];

	public function __construct($template)
	{
		$this->template = $template;
		bdump($template, 'hugo template');
	}

	protected function getTemplate()
	{
		$template = $this->template?? $this->cfg['errorTemplate'];
		return STATIC_DIR .$template;
	}
	
	// render previously declared template
	// @$page identifies source of page model data
    // extended classes must define this method
    abstract public function render($page);
	
}
