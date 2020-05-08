<?php

namespace Andygrond\Hugonette;

/* Anti CSRF Session Management
 * @author Andygrond 2019
**/

class Session
{
  public function __construct()
  {
    session_start();

    if (!isset($_SESSION['started_at'])) {
      $this->renewSession();
    } elseif (isset($_SESSION['closed_at']) && $_SESSION['closed_at'] < time() - 300) {
      $this->redirect('Delayed hijacking', '/');
    } elseif ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
      $this->redirect('Hijacking from another IP', '/');
    }
  }

  // log the incident and reload the page to given URL
  protected function redirect($reason, $url)
  {
    Log::warning($reason .' - session renewed.');
    header("Location: $url");
    exit;
  }

  // renew session optionally reloading the page to given URL
  protected function renewSession($reason)
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
