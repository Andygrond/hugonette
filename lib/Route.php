<?php

namespace Andygrond\Hugonette;

/* Simple fast routing for Hugonette
 * @author Andygrond 2020
**/

class Route
{
	private $page;					// page presentation parameters completed during routing process
	private $fullRequestPath;	// full URL path requested
	private $routeCounter = 0;	// counter of route trials

	// attributes passed as optional 3rd argument of group method
	private $attrib = [
		'presenterNamespace' => 'App\Presenters',
		'allowedMethods' => ['get', 'post'],
		'requestBase' => '',
		'requestPath' => null,
		'viewMode' => '',
	];

	// @$publishBase path to static pages (e.g. hugo public folder)
	// @$viewMode default view mode for the group: [ plain | latte | json ]
	public function __construct(string $publishBase, string $viewMode)
	{
		$this->page = new \stdClass;
		
		$this->attrib['publishBase'] = rtrim($publishBase, '/');
		$this->attrib['viewMode'] = $viewMode;
		
		[ $path ] = explode('?', urldecode($_SERVER['REQUEST_URI']));
		if (substr($path, -1) != '/') {
			$path .= '/';
		}
		$this->fullRequestPath = $path;
		
		$this->setRequestBase('');
	}
	
	// Log shutdown needed to write to file
	public function __destruct()
	{
		Log::close();	// effective only when set
	}
	
	// route for single request method
	// @method - http method as a route function name
	// @args = [$pattern, $model, $template]
	public function __call(string $method, array $args)
    {
		$this->routeCounter++;
		if ($this->checkMethod($method) && $this->regMatchPattern($args[0])) {
			$this->template(@$args[2]);
			$this->runPresenter($args[1]);
		}
		if (!in_array($method, $this->attrib['allowedMethods'])) {
			trigger_error("Router HTTP method: $method not allowed", E_USER_WARNING);
		}
   }

	// full static GET with one common presenter (runs all static pages at once)
	public function pages(string $presenter)
    {
		$this->routeCounter++;
		if ($this->checkMethod('GET')) {
			if ($this->template()) {
				$this->runPresenter($presenter);
//				$this->runPresenter($presenter, true);
			}
		}
    }

	// this can be used as the last routing directive in group or freely
	// redirect $to if URI simply starts from $pattern or $pattern is empty
	// @$permanent defaults to http code 301 Moved Permanently
	// @$permanent set to false = doesn/t inform search engines about the change
	public function redirect(string $pattern, string $to, bool $permanent = true)
    {
		$this->routeCounter++;
		if (!$pattern || $this->exactMatchPattern($pattern)) {
			(new Presenter())->redirect($permanent? 301 : 302, $to);
		}
	}
	
	// if applicable, should be placed as the last routing directive in a group - for not routed URI
	// @presenter usually shows status 404 with the native navigation panels
	public function notFound(string $presenter)
	{
		$this->routeCounter++;
		$this->runPresenter($presenter);
	}

	// register a set of routes with a set of shared attributes.
	public function group(string $pattern, \Closure $callback, array $attributes = [])
	{
		if ($this->exactMatchPattern($pattern)) {
			$parentAttributes = $this->attrib;

			if (count($attributes)) {
				$this->attrib = $attributes + $parentAttributes;
			}
			$this->setRequestBase($pattern);

			call_user_func($callback, $this);

			$this->attrib = $parentAttributes;
		}
	}

// ==================
	// checking http request method
	private function checkMethod(string $method): bool
	{
		return !strcasecmp($_SERVER['REQUEST_METHOD'], $method);
	}

	// run presenter instance and exit if truly presented
//	private function runPresenter(string $presenter, bool $static = false)
	private function runPresenter(string $presenter)
	{
//		$this->page->staticPages = $static;
		$this->page->view = $this->attrib['viewMode'];
		$this->page->publishBase = $this->attrib['publishBase'];
		$this->page->route[$this->routeCounter] = $presenter;	// route tracer

		PresenterFactory::create($presenter, $this->attrib['presenterNamespace'])->run($this->page);
	}
	
	// check regular expression pattern matching 
	// replace page params according to the pattern
	private function regMatchPattern(string $pattern): bool
	{
		$pattern = '@^' .$pattern .'$@';
		if (preg_match($pattern, $this->attrib['requestPath'], $params) === 1) {
			$this->page->params = $params;
			return true;
		}
		return false;
	}
	
	// simple pattern matching test - no variable parts
	private function exactMatchPattern(string $pattern): bool
	{
		return (strpos($this->attrib['requestPath'], $pattern) === 0);
	}
	
	// set template file name from given string if exists
	private function template(string $path = null): bool
	{
		$path = $path ?: $this->attrib['requestPath'];	// template file name derived from URL
		$template = $path .'index.html';
		
		if (is_file($this->attrib['publishBase'] .$template)) {
			$this->page->template = $template;
			return true;
		}
		return false;
	}

	// store request base and relative path
	// preset page params based on the path (valid for a route group)
	private function setRequestBase(string $requestBase)
	{
		$this->attrib['requestBase'] .= $requestBase;
		$path = $this->fullRequestPath;
		
		if ($this->attrib['requestBase'] && strpos($path, $this->attrib['requestBase']) === 0) {
			$path = substr($path, strlen($this->attrib['requestBase']));
		}
		$this->attrib['requestPath'] = $path;

		$this->page->params = explode('/', $path);
		$this->page->params[0] = $path;
	}

}
