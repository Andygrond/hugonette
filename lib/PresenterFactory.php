<?php

namespace Andygrond\Hugonette;

final class PresenterFactory
{

	// return instantiated presenter object
	public static function create($presenter, $namespace)
	{
		[ $class, $method ] = explode(':', $presenter .':default');
		$class = $namespace .'\\' .ucwords($class);

		return new $class($method);
	}

}
