<?php

namespace Andygrond\Hugonette;

/** Secure Session Management
 * @author Andygrond 2019
**/

class Session
{
  /**
  * @param redirectUrl - switch to another URL in case of danger
  * @param cookieOptions - non standard cookie options
  */
  public function __construct(string $redirectUrl = null, $cookieOptions = [])
  {
    if (session_status() != PHP_SESSION_ACTIVE || session_name() != 'Hugonette') {
      session_name('Hugonette');
      !$cookieOptions or session_set_cookie_params($cookieOptions);
      session_start();

      if (!$redirectUrl) {
        $redirectUrl = $_SERVER['REQUEST_URI'];
      }

      if (!isset($_SESSION['started_at'])) {
        Log::debug('Renewing session');
        $this->renewSession();
      } elseif (isset($_SESSION['closed_at']) && $_SESSION['closed_at'] < time() - 300) {
        $this->redirect('Delayed hijacking', $redirectUrl);
      } elseif ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
        $this->redirect('Hijacking from another IP', $redirectUrl);
      }
    }
  }

  // log the incident and reload the page to given URL
  protected function redirect(string $reason, string $redirectUrl = null)
  {
    Log::warning($reason .' - redirected to ' .$redirectUrl);
    $this->renewSession();

    if ($redirectUrl) {
      header("Location: $redirectUrl");
      exit;
    }
  }

  // renew the session optionally reloading the page to given URL
  protected function renewSession()
  {
    $time = time();
    $_SESSION['closed_at'] = $time;
    session_regenerate_id();
    $_SESSION['started_at'] = $time;
    unset($_SESSION['closed_at']);
    $_SESSION['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR']?? $_SERVER['REMOTE_ADDR'];
    $_SESSION['CSRF'] = [];
  }

}
