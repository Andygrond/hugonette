<?php

namespace Andygrond\Hugonette;

/* Page rendering
 * Andrzej Grondziowski 2019
**/

class PlainView
{
	private $template;
		
	public function __construct($template)
	{
		$this->template = $template;
		bdump($template, 'hugo template');
	}
	

	// render native or given template
	public function render($page)
	{
		$csrf = new CSRF_Session();

	// use page class declared
		$className = '\\Andygrond\\Pages\\' .$page;
		$page = new $className();
	
	// use native php template
		include($this->template);
	}
	
}
