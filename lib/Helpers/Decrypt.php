<?php

namespace Andygrond\Hugonette\Helpers;

// Secret data decrypter for Hugonette
// Andy Grondziowski 2020

use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Traits\JsonError;

class Decrypt
{
  use JsonError;

  private static $secret; // secret data once read
  private static $nonce;  // nonce once read

  // read secret data file
  public function __construct()
  {
    if (!self::$secret) {
      $encryptedFile = Env::get('base.system') .Env::get('hidden.file.access');
      self::$secret = unserialize(file_get_contents($encryptedFile));
      self::$nonce = array_shift(self::$secret);
    }
  }

  /**
  * @param sysname secret data key
  * @return - secret data for $sysname
  */
  public function get(string $sysname): ?object
  {
    $hash = md5($sysname);

    if (!isset(self::$secret[$hash])) {
      $error = 'Data not found for ' .$sysname;
    } else {
      $keyFile = Env::get('hidden.file.key');
      if (!is_file($keyFile)) {
        $error = 'Key file not found';
      } else {
        $json = sodium_crypto_secretbox_open(self::$secret[$hash], self::$nonce, include($keyFile));
        if ($json === false) {
          $error = $sysname .' decryption error';
        } else {
          $data = json_decode($json);
          if ($data === null) {
            $error = $sysname .' JSON error: ' .$this->jsonError();
          } else {
            return $data;
          }
        }
      }
    }

    trigger_error($error .' while decoding credentials');
  }

}
