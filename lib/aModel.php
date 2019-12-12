<?php

namespace Andygrond\Hugonette;

/* Abstract class for model data calculation
 * @author Andygrond 2019
**/

abstract class aModel
{

//	protected $model = [];
	protected $method;
	protected $params;

	public function __construct($method = 'default')
	{
		$this->method = $method;
	}
	
	// render declared template using $model class
	public function getModel($params = null)
	{
		$this->params = $params;
		bdump($params, 'params-model');
		
		return $this->{$this->method}();
	}
	
}
