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

  /** read secret data file
  * @param filename encrypted file name
  */
  public function __construct($filename)
  {
    if (!self::$secret) {
      self::$secret = unserialize(file_get_contents($filename));
      self::$nonce = array_shift(self::$secret);
    }
  }

  /**
  * @param key secret data key
  * @return - secret data for key $key
  */
  public function get(string $key): ?object
  {
    $hash = md5($key);

    if (!isset(self::$secret[$hash])) {
      $error = 'Data not found for ' .$key;
    } else {
      $keyFile = Env::get('hidden.file.key');
      if (!is_file($keyFile)) {
        $error = 'Key file not found';
      } else {
        $json = sodium_crypto_secretbox_open(self::$secret[$hash], self::$nonce, include($keyFile));
        if ($json === false) {
          $error = $key .' decryption error';
        } else {
          $data = json_decode($json);
          if ($data === null) {
            $error = $key .' JSON error: ' .$this->jsonError();
          } else {
            return $data;
          }
        }
      }
    }

    trigger_error($error .' while decoding credentials for ' .$key);
  }

}
