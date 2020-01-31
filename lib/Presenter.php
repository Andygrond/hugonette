<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * @author Andygrond 2020
**/

class Presenter
{
	protected $method;	// routed presenter method
	protected $template;	// routed model method
	protected $page;		// navigation data for response processing

	protected $cfg = [
		'homeUri' => HOME_URI,
	];
	
	public function __construct($method)
	{
		$this->method = $method;
	}
	
	// return model data using presenter method declared in router
	public function run($page)
	{
		$this->page = $page;
		$model = $this->{$this->method}();
		
		if ($model) {
			bdump($page, 'page');

			$template = $this->template ?: @$page->template;
			$view = new View($page->publishBase .$template);
			$view->{$page->view}($model);

			exit;
		}
	}
	
	public function redirect($code, $to)
	{
		if ($to[0] != '/' && strpos($to, '//') === false) {
			$to = $this->cfg->homeUri .$to;
		}
		Log::info($code .' Redirected to: ' .$to);
		header('Location: ' .$to, true, $code);
		
		exit;
	}
	
}
