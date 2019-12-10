<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugo websites
 * @author Andygrond 2019
**/

class Route
{
	private $view = null;		// view object
	private $template;			// base template
	private $viewMode = 'plain';	// view mode name
	private $requestPath;	// path of file requested
	private $requestFile;		// file requested
	
	private $cfg = [		// configuration data
		'requestBase' => HOME_URI,
		'publishBase' => STATIC_DIR,
		'errorTemplate' => ERROR_PAGE,
	];


	public function __construct()
	{
		$req = explode('?', urldecode($_SERVER['REQUEST_URI']));
		if (substr($req[0], -5) == '.html') {
			$pi = pathinfo($req[0]);
			$file = $pi['basename'];
			$path = $pi['dirname'];
			
		} else {
			$file = 'index.html';
			$path = $req[0];
		}

		if (strpos($path, $this->cfg['requestBase']) === 0) {
			$path = substr($path, strlen($this->cfg['requestBase']));
		}

		$this->requestFile = $file;
		$this->requestPath = trim($path, '/');
	}
	
	// shutdown handler
	public function __destruct()
	{
		if ($this->view === null) {	// page has not been found till now
			$this->render();
		}
		Log::close();
	}

	// set the mode of view for the subsequent routes
	// view modes: plain - latte - json
	public function setViewMode($mode)
	{
		$this->viewMode = ucwords($mode);
	}
	
	// route for single request method
	// @method - any http method expected as a function name
	// @args = [$pattern, $model, $template]
	public function __call($method, $args)
    {
		if ($this->checkMethod($method)) {
			if ($params = $this->matchPattern($args[0])) {
				$this->template = $args[2]?? $this->realTemplate();
				$this->render($args[1]);
			}
		}
    }

	// full static GET with one common model (all static pages)
	public function common($model)
    {
		if ($this->checkMethod('GET')) {
			if ($this->template = $this->realTemplate()) {
				$this->render($model);
			}
		}
    }

	// redirect $to if URI starts from $pattern
	// @$permanent defaults to 302 http code
	public function redirect($pattern, $to, $permanent = true)
    {
		if ($this->startPattern($pattern)) {
			$code = $permanent? 301 : 302;
			$this->redirection($code, $to);
		}
	}
	

// ==================
	// checking http request method
	private function checkMethod($method)
	{
		return !strcasecmp($_SERVER['REQUEST_METHOD'], $method);
	}

	// check pattern matching
	private function matchPattern($pattern)
	{
		$pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
		if (preg_match($pattern, '/' .$this->requestPath, $params) === 1) {
			bdump($params, 'params');
			return $params;
		}
		return false;
	}
	
	// check pattern starting from
	private function startPattern($pattern)
	{
		return (strpos($this->requestPath, $pattern) === 0);
	}

	// get template file name if exists
	private function realTemplate()
	{
		$path = $this->requestPath? $this->requestPath .'/' : '';
		$template = $this->cfg['publishBase'] .$path .$this->requestFile;
		return is_file($template)? $template : null;
	}

	private function redirection($code, $to)
	{
		if ($to[0] != '/' && strpos($to, '//') === false) {
			$to = self::$cfg['requestBase'] .$to;
		}
		Log::info($code .' Redirected to: ' .$to);
		header('Location: ' .$to, true, $code);
		$this->view = true;
		exit;
	}
	

	private function render($model = 'Homepage')
	{
		$viewClass = __NAMESPACE__ .'\\' .$this->viewMode .'View';
		$view = new $viewClass($this->template);
		$view->render(PageFactory::createPage($model));
		$this->view = true;
		exit;
	}
	
}
