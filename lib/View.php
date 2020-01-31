<?php

namespace Andygrond\Hugonette;

/* MVP View rendering for Hugonette
 * @author Andygrond 2020
**/

class View
{
	protected $template;

	protected $cfg = [
		'cacheLatte' => LIB_DIR .'temp/latte',
	];

	public function __construct($template = false)
	{
		$this->template = $template;
	}

	// render model data using Latte templating engine
	public function latte(&$_model)
	{
		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->cfg['cacheLatte']);

		bdump($_model, 'model');
		$latte->render($this->template, $_model);
	}

	// render model data using plain old PHP template
	public function plain(&$_model)
	{
		extract($_model);
		unset($_model);
		include($this->template);
	}
	
	// send model data as JSON object
	public function json(&$_model)
	{
		echo json_encode($_model, JSON_UNESCAPED_UNICODE);
	}
	
}
