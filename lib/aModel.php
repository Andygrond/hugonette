<?php

namespace Andygrond\Hugonette;

/* Abstract class for model data calculation
 * @author Andygrond 2019
**/

use Andygrond\Hugonette\Error;

abstract class aModel
{

	protected $model = [];
	protected $method;
	protected $statusCode = 200;
	
	// configuration data
	protected $cfg = [
		'errorBlock' => ERROR_BLOCK,
	];

	public function __construct($method = 'default')
	{
		$this->method = $method;
	}
	
	// render declared template using $model class
	public function getModel($args = null)
	{
		$this->model = $this->{$this->method}();
		return $this->model + $this->getShared();
	}
	
	// HTTP status page
	public function status($code)
	{
		Log::info("Error $code");
		$this->statusCode = $code;
		$this->method = 'error';
	}
	
	protected function error()
	{
		return [
			'status' => [
				'code' => $this->statusCode,
				'message' => Error::message($this->statusCode),
			],
			'template' => [
				'main' => $this->cfg['errorBlock'],
			],
		];
	}
	
}
