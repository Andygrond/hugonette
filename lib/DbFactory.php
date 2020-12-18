<?php

namespace Andygrond\Hugonette;

/* Database Factory Multiton for Hugonette
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Helpers\Decrypt;

class DbFactory
{
  private static $dblink = [];  // opened database connections

  // return instantiated database object, existing or new
  public static function create(string $target)
  {
    if (!@self::$dblink[$target]) {
      $dbaccess = Decrypt::data('/app/config/db.data')->get($target);
      $type = Env::get('namespace.db') .$dbaccess->type;
      self::$dblink[$target] = new $type($dbaccess);
    }

    return self::$dblink[$target];
  }

  // prevented instantiating
  private function __construct(){}

  // prevented cloning
  private function __clone(){}

  // prevented unserialization
  private function __wakeup(){}

}
