<?php

namespace Andygrond\Hugonette;

/* Database Factory for Hugonette
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Helpers\Decrypt;

final class DbFactory
{
  private static $dblink;  // otwarte połączenia do baz
  private static $decrypt; // Decrypt object

  // return instantiated database object
  public static function create(string $target)
  {
    if (!@self::$dblink[$target]) {
      if (!self::$decrypt) {
        self::$decrypt = new Decrypt(Env::get('hidden.file.access'));
      }
      $dbaccess = self::$decrypt->get($target);
      $type = Env::get('namespace.db') .$dbaccess->type;
      self::$dblink[$target] = new $type($dbaccess);
    }

    return self::$dblink[$target];
  }

}
