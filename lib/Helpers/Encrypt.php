<?php

namespace Andygrond\Hugonette\Helpers;

/** Secret data encrypter for Hugonette
 * Designed for independent use, not as a part of the system
 * @author Andy Grondziowski 2020
 */

use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Log;

class Encrypt
{
  private $secret = []; // secret data
  private $messages = []; // success messages
  private $newKeyFlag = false; // new key flag

  public function __construct()
  {
    function_exists('sodium_crypto_secretbox') or $this->quit('Lack of Sodium');
  }

  public function finalInfo()
  {
    $message = $this->messages? '<h3>Actions performed:</h3>' .implode("<br>\n", $this->messages) : '<h3>No action</h3>';

    echo '<!DOCTYPE html>
<html><head>
<meta charset="UTF-8">
<title>Encode</title>
</head><body>
' .$message .'
</body></html>';
  }

  /** Initialize secret data with a new set
  * @param orgFile ini formatted file name of original data
  */
  public function source(string $orgFile)
  {
    $this->newSecret();
    $file = $this->systemFile($orgFile);
    is_file($file) or $this->quit('Unable to read file ' .$orgFile);
    $data = parse_ini_file($file, true);
    is_array($data) or $this->quit('Unable to parse file ' .$orgFile);

    foreach ($data as $dataKey => $value) {
      $this->set($dataKey, $value);
    }

    $cnt = count($this->secret) -1;
    $s = ($cnt > 1)? 's' : '';
    $this->messages[] = "Found $cnt data chunk$s.";
    $this->messages[] = 'Please secure file: <b>' .$orgFile .'</b> and delete it from public space.';

    return $this;
  }

  /** Initialize secret data from file
  * @param secretFile encrypted file name
  */
  public function read(string $secretFile)
  {
    !$this->newKeyFlag or $this->quit('New key was generated - old data lost!');

    $file = $this->systemFile($secretFile);
    is_file($file) or $this->quit('Unable to read file ' .$secretFile);
    $this->secret = unserialize(file_get_contents($file));
    $this->messages[] = (count($this->secret) -1) .' data chunks in file: ' .$secretFile;

    return $this;
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

    (strlen($dataKey) >= 7) or $this->quit($dataKey .' - system name length must be at least 7');
    $this->secret[md5($dataKey)] = sodium_crypto_secretbox(json_encode($value), $this->secret[0], $key);

    return $this;
  }

  /**
  * @param secretFile encrypted file name
  */
  public function save(string $secretFile)
  {
    $file = $this->systemFile($secretFile);
    if (is_file($file)) {
      unlink($file) or $this->quit('Can not delete file: ' .$file);
    }

    file_put_contents($file, serialize($this->secret)) or $this->quit('File can not be written: ' .$file);
    $this->newKeyFlag = false;
    $this->messages[] = "Data file $secretFile is ready. ";
    Log::warning('Access data updated', $this->messages);

    return $this;
  }

  /** Delete encryption key file
  * Proceed with "do" variable defined in URL
  * @param deleteDir delete directory also?
  */
  public function destroyKey(string $keyCode = null, bool $deleteDir = null)
  {
    $keyFile = Env::get($keyCode?? 'hidden.file.key');
    if (is_file($keyFile)) {
      isset($_GET['do']) or $this->quit('Trying to delete key. Really know what you are doing?');
      unlink($keyFile) or $this->quit('Can not delete file: ' .$keyFile);
    }

    $dirName = pathinfo($keyFile)['dirname'];
    if ($deleteDir && is_dir($dirName)) {
      rmdir($dirName) or $this->quit('Can not delete directory: ' .$dirName);
    }

    return $this;
  }

  /** Encryption key generator
  * File name must be defined in Env hidden variable
  */
  public function newKey(string $keyCode = null)
  {
    !$this->secret or $this->quit('Key generation attempt on non-empty data set');

    $keyFile = Env::get($keyCode?? 'hidden.file.key');
    $dirName = pathinfo($keyFile)['dirname'];

    !is_file($keyFile) or $this->quit('Key replacement attempt! Not allowed.');
    if (!is_dir($dirName)) {
      mkdir($dirName) or $this->quit('Directory not created: ' .$dirName);
      chmod($dirName, 0700) or $this->quit('Directory not secured: ' .$dirName);
    }
    touch($keyFile) or $this->quit('Can not touch key file: ' .$keyFile);
    chmod($keyFile, 0600) or $this->quit('Can not secure key file: ' .$keyFile);

    $key = base64_encode(random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES));
    file_put_contents($keyFile, "<?php return base64_decode('$key');");
    $this->newKeyFlag = true;

    // test it
    (base64_encode($this->readKey()) == $key) or $this->quit('Uups... Incorrect key retrieved');
    $this->messages[] = "New encryption key $keyFile created.";

    return $this;
  }

  // =====
  // Read encryption key
  private function readKey(string $keyCode = null)
  {
    $keyFile = Env::get($keyCode?? 'hidden.file.key');
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

  // Get full path to the source or data file
  private function systemFile($filename)
  {
    if ($filename[0] != '/') {
      $filename = '/' .$filename;
    }
    return Env::get('base.system') .$filename;
  }

  /** $this->quit PHP notice with caller identification
  * @param message - output error message
  */
  public function quit($message)
  {
    $debug = debug_backtrace();
    $caller = array_pop($debug);
    $this->messages[] = "<b>$message</b> ...called in " .$caller['function'] .'() from ' .$caller['file'] .':' .$caller['line'];

    Log::warning('Unsuccessful attempt to update access data', $this->messages);
    exit();
  }

}
