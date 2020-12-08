<?php

namespace Andygrond\Hugonette\Helpers;

// Secret data decrypter for Hugonette
// Andy Grondziowski 2020

use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Traits\JsonError;

class Decrypt
{
  use JsonError;

  private $secret = []; // secret data once read
  private $nonce;  // nonce once read

  /** read secret data file
  * @param filename encrypted file name
  */
  public function __construct(string $filename)
  {
    if (!$this->secret) {
      $this->secret = unserialize(file_get_contents(Env::get('base.system') .$filename));
      $this->nonce = array_shift($this->secret);
    }
  }

  /**
  * @param key secret data key
  * @return - secret data for the $key
  */
  public function get(string $key): ?object
  {
    $hash = md5($key);

    if (!isset($this->secret[$hash])) {
      $error = 'Data not found for ' .$key;
    } else {
      $keyFile = Env::get('hidden.file.key');
      if (!is_file($keyFile)) {
        $error = 'Key file not found';
      } else {
        $json = sodium_crypto_secretbox_open($this->secret[$hash], $this->nonce, include($keyFile));
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
