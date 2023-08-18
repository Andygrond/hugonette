<?php

namespace Andygrond\Hugonette\Helpers;

/** Secret data decrypter Multiton for Hugonette
 * @author Andy Grondziowski 2020
 */

use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Traits\JsonError;

class Decrypt
{
  use JsonError;

  private static $instances = []; // remember secret data once read
  private $secret; // secret data
  private $nonce;  // nonce

 /** save secret data
  * unable to instatiate from outside the class
  * @param $secret encrypted secret data array
  */
  private function __construct(array $secret)
  {
    $this->nonce = array_shift($secret);
    $this->secret = $secret;
  }

 /** assign instance of Decrypt which uses given secret data file
  * @param $filename encrypted file name
  */
  public static function data(string $filename)
  {
    if (!isset(self::$instances[$filename])) {
      $filepath = Env::get('base.system') .$filename;
      if (is_file($filepath)) {
        self::$instances[$filename] = new self(unserialize(file_get_contents($filepath)));
      } else {
        trigger_error('Secret data file not found: ' .$filename);
      }
    }
    return self::$instances[$filename];
  }

 /**
  * @param $dataKey secret data key
  * @return - secret data for the $dataKey
  */
  public function get(string $dataKey, string $keyCode = null)
  {
    $hash = md5($dataKey);

    if (!isset($this->secret[$hash])) {
      $error = 'Data not found for ' .$dataKey;
    } else {
      $keyFile = Env::get($keyCode?? 'hidden.file.key');
      if (!is_file($keyFile)) {
        $error = 'Key file not found';
      } else {
        $json = sodium_crypto_secretbox_open($this->secret[$hash], $this->nonce, include($keyFile));
        if ($json === false) {
          $error = $dataKey .' decryption error';
        } else {
          $data = json_decode($json);
          if ($data === null) {
            $error = $dataKey .' JSON error: ' .$this->jsonError();
          } else {
            return $data;
          }
        }
      }
    }

    trigger_error($error .' while decoding secret data for ' .$dataKey);
  }

  // prevented cloning
  private function __clone(){}

  // prevented unserialization
  public function __wakeup(){}

}
