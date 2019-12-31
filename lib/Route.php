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
	private $publishBase;			// path to hugo public visible without trailing slash
	private $requestBase;			// request base folder
	private $requestPath;			// path of file requested
	
	private $cfg = [		// configuration data
		'requestBase' => HOME_URI,
		'publishBase' => STATIC_DIR,
		'allowedMethods' => ['get', 'put', 'post' ],
	];


	public function __construct()
	{
		$this->publishBase = rtrim($this->cfg['publishBase'], '/');
		$this->requestBase = rtrim($this->cfg['requestBase'], '/');

		$req = explode('?', urldecode($_SERVER['REQUEST_URI']));
		$file = 'index.html';
		$path = $req[0];

		if ($path[strlen($path)-1] != '/') {
			$path .= '/';
		}
		if (strpos($path, $this->requestBase) === 0) {
			$path = substr($path, strlen($this->requestBase));
		}

		$this->requestPath = $path;
		bdump([
			'publishBase' => $this->publishBase,
			'requestBase' => $this->requestBase,
			'requestPath' => $this->requestPath,
		]);
	}
	
	// shutdown handler
	public function __destruct()
	{
		Log::close();	// effective only when set
	}
	
	// not routed pages - show status 404
	public function notFound()
	{
		$this->template = $this->publishBase .'/index.html';
		$this->render('Error:_404', $this->realParams());
	}

	// set view mode for the subsequent routes
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
		if (!in_array($method, $this->cfg['allowedMethods'])) {
			throw new \Exception('Router HTTP method not allowed: ' .$method);
		}

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
				$this->render($model, $this->realParams());
			}
		}
    }

	// redirect $to if URI starts from $pattern
	// @$permanent defaults to 302 http code
	public function redirect($pattern, $to, $permanent = true)
    {
		if (strpos($this->requestPath, $pattern) === 0) {
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
		if (preg_match($pattern, $this->requestPath, $params) === 1) {
			return $params;
		}
		return false;
	}
	
	// get template file name from URI if exists
	private function realTemplate()
	{
		$template = $this->publishBase .$this->requestPath .'index.html';
		return is_file($template)? $template : null;
	}

	// get params from URI
	private function realParams()
	{
		$params = explode('/', $this->requestPath);
		$params[0] = $this->requestPath;
		return $params;
	}

	// get template file name from given string if exists
	private function getTemplate($path)
	{
		$template = $this->publishBase .$path .'index.html';
		return is_file($template)? $template : null;
	}

	private function redirection($code, $to)
	{
		if ($to[0] != '/' && strpos($to, '//') === false) {
			$to = $this->cfg['requestBase'] .$to;
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
