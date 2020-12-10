<?php

namespace Andygrond\Hugonette\Helpers;

/** Secret data encrypter for Hugonette
 * Designed for independent use, not as a part of the system
 * @author Andy Grondziowski 2020
 */

use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Traits\JsonError;

class Encrypt
{
  use JsonError;

  private $secret = []; // secret data
  private $key;    // key

  /** read secret data file
  * @param filename encrypted file name
  */
  public function __construct(string $filename)
  {
    function_exists('sodium_crypto_secretbox') or exit('Lack of Sodium');

    if (!$this->secret) {
      $this->secret = unserialize(file_get_contents(Env::get('base.system') .$filename));
      $this->nonce = array_shift($this->secret);
    }
  }

  /**
  * @param orgFile file name of original JSON encoded data
  * @param secretFile encrypted file name
  */
  public function copy(string $orgFile, string $secretFile)
  {
    $this->readKey();
    if (is_file($secretFile)) {
      unlink($secretFile) or exit('Can not delete file: ' .$secretFile);
    }

    $data = $this->getJson($orgFile);
    $this->secret[0] = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    foreach ($data as $dataKey => $value) {
      $this->setData($dataKey, $value);
    }

    file_put_contents($secretFile, serialize($this->secret)) or exit('File can not be written: ' .$secretFile);
    echo "Data file $secretFile is ready. ";
    unlink($orgFile) or exit('Can not delete file: ' .$orgFile .' - please do it yourself');
  }

  /** Encryption key generator
  * File name must be defined in Env hidden variable
  */
  public function generateKey()
  {
    $keyFile = Env::get('hidden.file.key');
    $dirName = pathinfo($keyFile)['dirname'];

    if (is_dir($dirName)) {
      if (is_file($keyFile)) {
        unlink($keyFile) or exit('Can not delete file: ' .$keyFile);
      }
    } else {
      mkdir($dirName) or exit('Directory not created: ' .$dirName);
      chmod($dirName, 0700) or exit('Directory not secured: ' .$dirName);
    }

    touch($keyFile) or exit('Can not touch key file: ' .$keyFile);
    chmod($keyFile, 0600) or exit('Can not secure key file: ' .$keyFile);

    $key = base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
    file_put_contents($keyFile, "<?php return base64_decode('$key');");
    echo "New encryption key $keyFile created. ";
  }

  private function readKey()
  {
    if ($this->key) return;
    $keyFile = Env::get('hidden.file.key');
    is_file($keyFile) or exit ('No key file ' .$keyFile);
    $key = include $keyFile;
    $key or exit('No key available');
    (strlen($key) == SODIUM_CRYPTO_SECRETBOX_KEYBYTES) or exit ('Invalid key size');
    $this->key = $key;
  }

  private function getJson(string $filename)
  {
    is_file($orgFile) or exit('File not found: ' .$orgFile);
    $json = file_get_contents($orgFile);
    $json or exit('Can not read file: ' .$orgFile);
    $data = json_decode($json);
    $data or exit(jsonError() .' in ' .$orgFile);
    return $data;
  }

  private function setData($dataKey, $value)
  {
  	(strlen($dataKey) >2) or exit ("System name $dataKey length must be at least 3 (preferebly 8 or more)");
  	$hash = md5($dataKey);
  	!isset($this->secret[$hash]) or exit ("System name $dataKey is doubled or too similar to another");
    $this->secret[$hash] = sodium_crypto_secretbox(json_encode($value), $this->secret[0], $this->key);
  }

}
