<?php

namespace Andygrond\Hugonette;

/* CSRF Token Session Management
 * @author Andygrond 2019
 * inspired by https://github.com/paragonie/anti-csrf
**/

class Token
{
  protected $debug;
  protected $cfg = [
    'tokenLifetime' => 900,
    'recycleAfter' => 512,
    'indexKey' => 'csrf_index';
    'tokenKey' => 'csrf_token';
  ];

  public function __construct($debug = false, $cfg = [])
  {
    $this->debug = $debug;
    $this->cfg = $cfg + $this->cfg;
  }

  // place token and index inside the form
  public function hiddenInput($lock = false)
  {
    self::log('Hidden input generation');
    $token_array = $this->getToken($lock);
    return implode(
      array_map(
        function(string $key, string $value): string {
          return "<input type=\"hidden\" name=\"$key\" value=\"" .self::noHTML($value) ."\"/>\n";
        },
        array_keys($token_array), $token_array
      )
    );
  }

  // new token generator
  // lock can be given in case of AJAX
  public function getToken($lock = false): array
  {
    $lock = $this->getLock($lock);
    $index = bin2hex(random_bytes(17));
    $token = bin2hex(random_bytes(33));

    $session =& $_SESSION['CSRF'];
    $session[$index] = [
      'created_at' => time(),
      'token' => $token,
      'lock' => $lock,
    ];

    while (count($session) > $this->cfg['recycleAfter']) {	// recycle if too much tokens
      array_shift($session);
      self::log('First token recycled');
    }

    return [
      $this->cfg['indexKey'] => $index,
      $this->cfg['tokenKey'] => $token,
    ];
  }

  // validate a request token with session token
  public function validateRequest($post = false): int
  {
    if (!$post) {
      $post =& $_POST;
    }
    $index = @$post[$this->cfg['indexKey']];
    $token = @$post[$this->cfg['tokenKey']];
    if (!$index || !$token) {
      return 1;
    }
    if (!is_string($index) || !is_string($token)) {
      return 2;
    }

    if (!$stored = $this->getStoredToken($index)) {
      self::log('Token not found');
      return 3;
    }
    if ($this->tokenExpired($stored)) {
      self::log('Token expired');
      return 4;
    }

    $lock = $this->getLock();
    if (!hash_equals($lock, (string) $stored['lock'])) {
      self::log("$lock origin does not match lock {$stored['lock']}");
      return 5;
    }
    if (!hash_equals($token, $stored['token'])) {
      self::log('Wrong token');
      return 6;
    }

    self::log('Token validated');
    return 0;
  }

  public function getStoredToken(string $index): array
  {
    if (!isset($_SESSION['CSRF'])) {
      return false;
    }
    $session =& $_SESSION['CSRF'];

    if (!isset($session[$index])) {	// token not found
      return false;
    }
    $stored = $session[$index];
    unset($session[$index]);	// delete used token
    self::log('Token deleted after use');

    return $stored;
  }

  public function tokenExpired(array $token): bool
  {
    if (empty($token['created_at'])) {
      return true;
    }
    $time = $token['created_at'] + $this->cfg['tokenLifetime'];
    return $time < time();
  }

  // ignore trailing slashes
  public function getLock($lock = false): string
  {
    $lock = '@' .($lock? $lock : $_SERVER['REQUEST_URI']);
    if (preg_match('#/$#', $lock)) {
      $lock = substr($lock, 0, -1);
    }
    self::log("Lock taken: $lock");

    return $lock;
  }

  // set configuration values
  protected static function noHTML(string $untrusted): string
  {
    return htmlentities($untrusted, ENT_QUOTES, 'UTF-8');
  }

  protected static function log(string $message)
  {
    if ($this->debug) {
      Log::info($message);
    }
  }

}
