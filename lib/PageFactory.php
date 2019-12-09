<?php

namespace Andygrond\Hugonette;

final class PageFactory
{

	// configuration data
	private static $cfg = [
		'modelDir' => LIB_DIR .'app/model/',
		'modelNamespace' => 'App\\Model\\',
	];
	
	public static $methodName;

	// return instantiated model object
	public static function createPage($model)
	{
		[ $className, self::$methodName ] = explode(':', $model .':default');
		require self::$cfg['modelDir'] .$className .'.php';
		$className = self::$cfg['modelNamespace'] .$className;
		return new $className();
	}

	// resolve symbolic model notation
	public function resolveModel()
	{
		
	}

}
