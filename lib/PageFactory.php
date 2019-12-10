<?php

namespace Andygrond\Hugonette;

final class PageFactory
{

	// configuration data
	private static $cfg = [
//		'modelDir' => LIB_DIR .'app/model/',
		'modelNamespace' => 'App\\Model\\',
	];
	
	// return instantiated model object
	public static function createPage($model)
	{
		[ $class, $method ] = explode(':', $model .':default');
		bdump($class .':' .$method, 'model class');
		
//		require self::$cfg['modelDir'] .$class .'.php';
		$class = self::$cfg['modelNamespace'] .ucwords($class);
		return new $class($method);
	}

}
