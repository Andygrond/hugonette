<?php

namespace Andygrond\Hugonette;

/** HTTP response finisher for Hugonette
 * Finish HTTP connection and continue processing
 * Thanks to https://stackoverflow.com/questions/15273570/continue-processing-php-after-sending-http-response/28738208
 * @author Andygrond 2021
 */

class FinishResponse
{
  private $nginx;

  public function __construct()
  {
    $this->nginx = is_callable('fastcgi_finish_request');
    $this->nginx or ob_start();
  }

  public function finish()
  {
    ignore_user_abort(true);
    set_time_limit(0);
    Log::debug('Immediate finish of HTTP response');

    if ($this->nginx) {
      // This works in Nginx
      session_write_close();
      fastcgi_finish_request();
    } else {
      $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
      header($serverProtocol .' 200 OK');
      header('Content-Encoding: none');
      header('Content-Length: '.ob_get_length());
      header('Connection: close');

      ob_end_flush();
      ob_flush();
      flush();
    }
  }
}
