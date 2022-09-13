<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
// use Tracy\Debugger;

class Bootstrap
{
  public static function boot(): Configurator
  {
//    Debugger::timer();

    $configurator = new Configurator;

	$configurator->setDebugMode(OP_MODE == 'development'); 	// application debug mode
//	$configurator->setDebugMode('10.36.51.98'); // enable for my remote IP

    $configurator->setTimeZone('Europe/Warsaw');
    $configurator->setTempDirectory(__DIR__ . '/../temp');

    // autoload local app classes
    $configurator->createRobotLoader()
      ->addDirectory(__DIR__)
      ->setAutoRefresh(OP_MODE != 'production')  // remember to delete cache on production
      ->register();

    return $configurator;
  }
}
