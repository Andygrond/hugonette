<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugo websites
 * @author Andygrond 2019
**/

class Route
{
	private $rendered = false;		// page was rendered
	private $template;					// base template
	private $viewMode = 'plain';	// view mode name
	private $requestPath;			// path of file requested
	private $requestFile;				// file requested
	
	private $cfg = [		// configuration data
		'requestBase' => HOME_URI,
		'publishBase' => STATIC_DIR,
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
		if (!$this->rendered) {	// page has not been routed till now
//			$this->clean();
		}
		Log::close();
	}
	
	// not routed pages - send 404
	public function clean()
	{
		$this->template = $this->cfg['publishBase'] .'index.html';
		$this->render('Error:_404');
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
				$this->template = @$args[2]? $this->getTemplate($args[2]) : $this->realTemplate();
				$this->render($args[1], $params);
			}
		}
    }

	// full static GET with one common model (all static pages)
	public function pages($model)
    {
		if ($this->checkMethod('GET')) {
			if ($this->template = $this->realTemplate()) {
				$this->render($model, $this->getParams());
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
		$pattern = '@^' .$pattern .'$@';
		if (preg_match($pattern, '/' .$this->requestPath, $params) === 1) {
			return $params;
		}
		return false;
	}
	
	// get params of current page
	private function getParams()
	{
		preg_match('@^[^/]+$@', $this->requestPath, $params);
		return $params;
	}
	
	// check pattern starting from
	private function startPattern($pattern)
	{
		return (strpos($this->requestPath, $pattern) === 0);
	}

	// get template file name from URI if exists
	private function realTemplate()
	{
		$path = $this->requestPath? $this->requestPath .'/' : '';
		$template = $this->cfg['publishBase'] .$path .$this->requestFile;
		return is_file($template)? $template : null;
	}

	// get template file name from given string if exists
	private function getTemplate($path)
	{
		if (substr($path, -1) == '/') {
			$path .= 'index.html';
		}
		$template = $this->cfg['publishBase'] .ltrim($path, '/');
		return is_file($template)? $template : null;
	}

	private function redirection($code, $to)
	{
		if ($to[0] != '/' && strpos($to, '//') === false) {
			$to = self::$cfg['requestBase'] .$to;
		}
		Log::info($code .' Redirected to: ' .$to);
		header('Location: ' .$to, true, $code);
		$this->rendered = true;
		exit;
	}
	
	// instantiate view class and render the page
	private function render($model, $params = [])
	{
		$viewClass = __NAMESPACE__ .'\\' .$this->viewMode .'View';
		$view = new $viewClass($this->template);
		$view->render(PageFactory::createPage($model, $params));
		$this->rendered = true;
		exit;
	}
	
}
