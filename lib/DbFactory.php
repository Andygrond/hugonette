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
  public static function create(string $profile)
  {
    if (!@self::$dblink[$profile]) {
      $file = Env::get('hidden.file.db');
      $dbaccess = Decrypt::data($file)->get($profile);
      $type = Env::get('namespace.db') .$dbaccess->type;
      self::$dblink[$profile] = new $type($dbaccess);
    }

    return self::$dblink[$profile];
  }

  // prevented instantiating
  private function __construct(){}

  // prevented cloning
  private function __clone(){}

  // prevented unserialization
  public function __wakeup(){}

}
