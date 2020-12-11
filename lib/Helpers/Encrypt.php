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
  private $messages = []; // success messages
  private $newKey = false; // new key flag

  public function __construct()
  {
    function_exists('sodium_crypto_secretbox') or $this->quit('Lack of Sodium');
  }

  public function __destruct()
  {
    echo '<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>Encode</title>
</head><body>
<h3>Actions performed:</h3>
' .implode("<br>\n", $this->messages) .'
</body></html>';
  }

  /** Initialize secret data with a new set
  * @param orgFile ini formatted file name of original data
  */
  public function source(string $orgFile)
  {
    $this->newSecret();
    $file = Env::get('base.system') .$orgFile;
    $data = parse_ini_file($file, true);

    foreach ($data as $dataKey => $value) {
      $this->set($dataKey, $value);
    }

    $cnt = count($this->secret) -1;
    $this->messages[] = "Initialized with $cnt data chunks";
    $this->messages[] = 'Please secure file: <b>' .$orgFile .'</b> and delete it from public place';
  }

  /** Initialize secret data from file
  * @param secretFile encrypted file name
  */
  public function read(string $secretFile)
  {
    !$this->newKey or $this->quit('New key was generated - old data lost!');

    $file = Env::get('base.system') .$secretFile;
    is_file($file) or $this->quit('Unable to read file ' .$secretFile);
    $this->secret = unserialize(file_get_contents($file));
    $this->messages[] = (count($this->secret) -1) .' data chunks in file: ' .$secretFile;
  }

  /** append or replace a data chunk
  * @param dataKey key for data chunk
  * @param value data chunk itself
  */
  public function set($dataKey, $value)
  {
    static $key;
    $key or $key = $this->readKey();
    $this->secret or $this->newSecret();

    (strlen($dataKey) >= 7) or $this->quit("System name $dataKey length must be at least 7");
    $this->secret[md5($dataKey)] = sodium_crypto_secretbox(json_encode($value), $this->secret[0], $key);
  }

  /**
  * @param secretFile encrypted file name
  */
  public function save(string $secretFile)
  {
    $file = Env::get('base.system') .$secretFile;
    if (is_file($file)) {
      unlink($file) or $this->quit('Can not delete file: ' .$file);
    }

    file_put_contents($file, serialize($this->secret)) or $this->quit('File can not be written: ' .$file);
    $this->messages[] = "Data file $secretFile is ready. ";
  }

  /** Encryption key generator
  * File name must be defined in Env hidden variable
  */
  public function newKey()
  {
    !$this->secret or $this->quit('Key generation attempt on non-empty data set');

    $keyFile = Env::get('hidden.file.key');
    $dirName = pathinfo($keyFile)['dirname'];

    if (is_dir($dirName)) {
      if (is_file($keyFile)) {
        unlink($keyFile) or $this->quit('Can not delete file: ' .$keyFile);
      }
    } else {
      mkdir($dirName) or $this->quit('Directory not created: ' .$dirName);
      chmod($dirName, 0700) or $this->quit('Directory not secured: ' .$dirName);
    }

    touch($keyFile) or $this->quit('Can not touch key file: ' .$keyFile);
    chmod($keyFile, 0600) or $this->quit('Can not secure key file: ' .$keyFile);

    $key = base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
    file_put_contents($keyFile, "<?php return base64_decode('$key');");
    $this->newKey = true;

    // test it
    (base64_encode($this->readKey()) == $key) or $this->quit('Uups... Incorrect key retrieved');

    $this->messages[] = "New encryption key $keyFile created.";
  }

  // Read encryption key
  private function readKey()
  {
    $keyFile = Env::get('hidden.file.key');
    is_file($keyFile) or $this->quit('No key file ' .$keyFile);
    $key = include $keyFile;
    $key or $this->quit('No key available');
    (strlen($key) == SODIUM_CRYPTO_SECRETBOX_KEYBYTES) or $this->quit('Invalid key size');
    return $key;
  }

  // Initialize secret data with new nonce
  private function newSecret()
  {
    $this->secret = [];
    $this->secret[0] = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
  }

  // Read JSON encoded data
  private function getJson(string $orgFile)
  {
    is_file($orgFile) or $this->quit('File not found: ' .$orgFile);
    $json = file_get_contents($orgFile);
    $json or $this->quit('Can not read file: ' .$orgFile);
    $data = json_decode($json);
    $data or $this->quit(jsonError() .' in ' .$orgFile);
    return $data;
  }

  /** $this->quit PHP notice with caller identification
  * @param message - output error message
  */
  public function quit($message)
  {
    $caller = @debug_backtrace()[2];
    $this->messages[] = "<b>$message</b> ...called in " .$caller['function'] .'() from ' .$caller['file'] .':' .$caller['line'];

    exit();
  }

}
