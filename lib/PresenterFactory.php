<?php

namespace Andygrond\Hugonette;

final class PresenterFactory
{

	// configuration data
	private static $cfg = [
		'presenterNamespace' => 'App\\Presenters\\',
	];
	
	// return instantiated presenter object
	public static function create($presenter)
	{
		[ $class, $method ] = explode(':', $presenter .':default');
		$class = self::$cfg['presenterNamespace'] .ucwords($class);

		return new $class($method);
	}

}
