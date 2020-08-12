<?php

namespace Andygrond\Hugonette\Access;

/* NTLM protocol basic authentication on Apache httpd
 * Retrieves Windows user name in corporate intranet environment with Active Directory
 * Warning: NO real authentication process is applied; user name is retrieved from Windows authentication
 * Based on mere presumption that logged in person uses the client workstation in this moment
 * Not reliable for severe security demands
 * On Firefox add server to whitelist: network.automatic-ntlm-auth.trusted-uris at the page: about:config
 * Author: Andygrond 2020
 * Inspired by https://loune.net/2007/10/simple-lightweight-ntlm-in-php/
**/

use Andygrond\Hugonette\Log;

class Ntlm
{
  private $auth;  // authorization header

  // check authorization header
  public function __construct()
  {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])){
      header('HTTP/1.1 401 Unauthorized');
      header('WWW-Authenticate: NTLM');
      exit;
    }
    $this->auth = @$headers['Authorization'];
  }

  // get authorization
  public function auth(bool $debug = false) {
    if (substr($this->auth,0,5) == 'NTLM ') {
      $msg = base64_decode(substr($this->auth, 5));
      if (substr($msg, 0, 8) != "NTLMSSP\x00") {
        return [
          'error' => 'NTLM header not recognized',
        ];
      }

      switch ($msg[8]) {
        case "\x01":
          $this->authRequest();
          exit;
        case "\x03":
          $details = $this->getDetails($msg);
          Log::info('NTLM user', $details);
          return $details;
        default:
          Log::warning('NTLM message error');
          return [
            'error' => "NTLM message error",
          ];
      }
    }
  }

  // decode NTLM message
  private function getDetails(string $msg): array
  {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR']?? $_SERVER['REMOTE_ADDR'];
    $host = strtolower(gethostbyaddr($ip));
    $h = explode('.', $host);

    $hname = $this->getMsg($msg, 44);
    $domain = $this->getMsg($msg, 28);

    $_SESSION['user'] = [
      'login' => $this->getMsg($msg, 36),
      'ip' => $ip,
      'host' => $host,
    ];

    if ($h[0] != $hname || $h[1] != $domain) {
      $_SESSION['user']['error'] = "Host $host mismatch: $hname.$domain";
    }
    return $_SESSION['user'];
  }

  private function authRequest()
  {
    $msg2 = "NTLMSSP\x00\x02\x00\x00\x00".
    "\x00\x00\x00\x00\x00\x00\x00\x00". // target name
    "\x01\x02\x81\x00". // flags
    "\x00\x00\x00\x00\x00\x00\x00\x00". // challenge
    "\x00\x00\x00\x00\x00\x00\x00\x00". // context
    "\x00\x00\x00\x00\x00\x00\x00\x00"; // target info len/alloc/offset
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: NTLM '.trim(base64_encode($msg2)));
  }

  private function getMsg(string $msg, int $start): string
  {
    $len = 256 * ord($msg[$start+1]) + ord($msg[$start]);
    $off = 256 * ord($msg[$start+5]) + ord($msg[$start+4]);
    return strtolower(str_replace("\0", '', substr($msg, $off, $len)));
  }

}
