<?php

namespace Andygrond\Hugonette;

final class PageFactory
{

	// configuration data
	private static $cfg = [
		'modelNamespace' => 'App\\Model\\',
	];
	
	// return instantiated model object
	public static function createPage($model, $params)
	{
		[ $class, $method ] = explode(':', $model .':default');
		bdump($class .':' .$method, 'model class');
		
		$class = self::$cfg['modelNamespace'] .ucwords($class);
		return new $class($method, $params);
	}

}
