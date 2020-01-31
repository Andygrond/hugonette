<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugo websites
 * @author Andygrond 2020
**/

class Route
{
	private $requestBase;		// request base folder
	private $requestPath;		// path of real URI requested
	private $page;					// page presentation parameters completed during routing process
	private $routeCounter = 0;	// counter of route trials
	private $allowedMethods = ['get', 'put', 'post', 'delete'];

	public function __construct()
	{
		$this->requestBase = rtrim(HOME_URI, '/');
		$this->page = new \stdClass;
		$this->page->publishBase = rtrim(STATIC_DIR, '/');	// path to visible hugo public

		$this->requestPath = $this->getRequestPath();
		$this->setView('latte');
	}
	
	// Log shutdown needed to write to file
	public function __destruct()
	{
		Log::close();	// effective only when set
	}
	
	// set view mode for the subsequent routes
	// view modes: plain - latte - json
	public function setView($view)
	{
		$this->page->view = $view;
	}
	
	// should be the last of routing directives
	// not routed URI - usually show status 404 or redirect to Homepage
	public function notFound($presenter)
	{
		$this->routeCounter++;
		$this->runPresenter($presenter);
	}

	// route for single request method
	// @method - any http method expected as a function name
	// @args = [$pattern, $model, $template]
	public function __call($method, $args)
    {
		$this->routeCounter++;
		if (in_array($method, $this->allowedMethods)) {
			if ($this->checkMethod($method)) {
				if ($this->matchPattern($args[0])) {
					$this->template(@$args[2]);
					$this->runPresenter($args[1]);
				}
			}
		} else {
			trigger_error("Router HTTP method: $method not allowed", E_USER_WARNING);
		}
   }

	// full static GET with one common presenter (all static pages)
	public function pages($presenter)
    {
		$this->routeCounter++;
		if ($this->checkMethod('GET')) {
			if ($this->template()) {
				$this->runPresenter($presenter, true);
			}
		}
    }

	// redirect $to if URI simply starts from $pattern
	// @$permanent defaults to 302 http code
	public function redirect($pattern, $to, $permanent = true)
    {
		$this->routeCounter++;
		if (strpos($this->requestPath, $pattern) === 0) {
			$page = new Presenter('');
			$page->redirect($permanent? 301 : 302, $to);
		}
	}
	

// ==================
	// checking http request method
	private function checkMethod($method)
	{
		return !strcasecmp($_SERVER['REQUEST_METHOD'], $method);
	}

	// run presenter instance and exit if presented
	// when this case appears not relevant - presenter should return false model
	private function runPresenter($presenter, $static = false)
	{
		$this->page->staticView = $static;
		$this->page->route[$this->routeCounter] = $presenter;	// route tracer
		PresenterFactory::create($presenter)->run($this->page);
	}
	
	// check pattern matching and replace page params according to the pattern
	private function matchPattern($pattern)
	{
		$pattern = '@^' .$pattern .'$@';
		if (preg_match($pattern, $this->requestPath, $params) === 1) {
			$this->page->params = $params;
			return true;
		}
		return false;
	}
	
	// get template file name from given string if exists
	private function template($path = null)
	{
		$path = $path ?: $this->requestPath;	// template file name from URI
		$this->page->template = $path .'index.html';
		return is_file($this->page->publishBase .$this->page->template);
	}

	// get real request path from URI
	private function getRequestPath()
	{
		$req = explode('?', urldecode($_SERVER['REQUEST_URI']));
		$path = $this->cleanRequestPath($req[0]);

		$this->page->params = $this->getParams($path);
		return $path;
	}

	// request path formatting
	private function cleanRequestPath($path)
	{
		if ($path[strlen($path)-1] != '/') {
			$path .= '/';
		}
		if (strpos($path, $this->requestBase) === 0) {
			$path = substr($path, strlen($this->requestBase));
		}
		return $path;
	}

	// prepare page params from request path
	private function getParams($path)
	{
		$params = explode('/', $path);
		$params[0] = $path;
		return $params;
	}
	
}
