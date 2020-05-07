<?php

namespace Andygrond\Hugonette;

/* CSRF Token Session Management
* @author Andygrond 2019
* inspired by Paragon Initiative Enterprises <https://paragonie.com> AntiCSFR class
**/

class Session
{
  protected $cfg = [
    'tokenLifetime' => 900,
    'recycleAfter' => 512,
  ];

  protected $indexKey = 'csrf_index';
  protected $tokenKey = 'csrf_token';
  protected $debug;

  public function __construct($debug = false, $cfg = [])
  {
    $this->debug = $debug;
    $this->cfg = $cfg + $this->cfg;
    session_start();

    if (!isset($_SESSION['started_at'])) {
      $this->renewSession('Start');
    } elseif (isset($_SESSION['closed_at']) && $_SESSION['closed_at'] < time() - 300) {
      $this->renewSession('Delayed hijacking', '/');
    } elseif ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
      $this->renewSession('Hijacking from another IP', '/');
    }
  }

  // renew session optionally reloading the page to given URL
  protected function renewSession($reason, $redirect = false)
  {
    $time = time();
    $_SESSION['closed_at'] = $time;
    session_regenerate_id();
    $_SESSION['started_at'] = $time;
    unset($_SESSION['closed_at']);
    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['CSRF'] = [];
    self::log($reason .' - session renewed.');

    if ($redirect)
    header("Location: $redirect");
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
      $this->indexKey => $index,
      $this->tokenKey => $token,
    ];
  }

  // validate a request token with session token
  public function validateRequest($post = false): int
  {
    if (!$post) {
      $post =& $_POST;
    }
    $index = @$post[$this->indexKey];
    $token = @$post[$this->tokenKey];
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
