<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * @author Andygrond 2020
**/

class Presenter
{
	private $method;		// routed presenter method
	protected $page;		// navigation data for response processing

	protected $template;	// template set in router or in presenter method
	protected $view;		// view type set in router or in presenter method

	public function __construct(string $method = 'default')
	{
		$this->method = $method;
	}
	
	// return model data using presenter method declared in router
	// presenter method will return an array of model data
	// when this case appears not relevant - presenter method will return false
	public function run(\stdClass $page)
	{
		$this->page = $page;
		$this->view = $page->view;
		
		$model = $this->{$this->method}();	// presenter method call
		
		if ($model !== false) {
			bdump($page, 'page');

			$template = $this->template ?: @$page->template;
			(new View($page->publishBase .$template))->{$this->view}($model);

			exit;
		}
	}
	
	// redirect @$to if URI simply starts from $pattern or $pattern is empty
	// @$permanent in Presenter defaults to http code 302 Found
	public function redirect(string $to, bool $permanent = false)
	{
		$code = $permanent? 301 : 302;
		Log::info($code .' Redirected to: ' .$to);
		header('Location: ' .$to, true, $code);
		
		exit;
	}
	
}
