<?php

namespace Andygrond\Hugonette;

/* Abstract class for model data calculation
 * @author Andygrond 2019
**/

abstract class aModel
{

	protected $shared = [];		// shared model components
	protected $params = [];	// page parameters
	protected $method;			// routed model methods

	public function __construct($method, $params)
	{
		bdump($params, 'params');
		$this->method = $method;
		$this->assign($params);
	}
	
	// render declared template using $model class
	public function getModel()
	{
		return $this->{$this->method}() + $this->shared;
	}
	
	// collect and name all URL, GET, POST params
	abstract protected function assign($params);
	
}
