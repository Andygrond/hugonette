<?php

declare(strict_types=1);

namespace App;

use Nette\Configurator;
use Andygrond\Hugonette\Env;


class Bootstrap
{
  public static function boot(): Configurator
  {
    define('SYS_DIR', dirname(__DIR__));
    Env::init(SYS_DIR);
    Env::set('mode', 'development');  // app operating mode [development | production | maintenance]

    $configurator = new Configurator;
    $configurator->setDebugMode(true);  // set false on production
//  $configurator->setDebugMode('secret@10.36.51.98'); // enable for my remote IP

    $configurator->enableTracy(SYS_DIR .'/log');
    $configurator->setTimeZone('Europe/Warsaw');
    $configurator->setTempDirectory(SYS_DIR .'/temp');

    // autoload local app classes
    $configurator->createRobotLoader()
      ->addDirectory(__DIR__)
      ->setAutoRefresh(true)  // when set to false, remember to delete Latte cache after update
      ->register();

  }
}
