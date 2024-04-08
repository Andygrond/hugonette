<?php

namespace Andygrond\Hugonette\Access;

/* JSON API data coder and decoder with http(s) transport
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Helpers\Status;
use Andygrond\Hugonette\Traits\JsonErrorTrait;
use Andygrond\Hugonette\Log;

class JsonApi
{
  use JsonErrorTrait;

  protected $ch;  // curl handle
  protected $url; // last url request
  protected $logFile; // optional log

  public function __construct($timeout = 300)
  {
    $this->ch = curl_init();

    curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($this->ch, CURLOPT_FAILONERROR, true);
    curl_setopt($this->ch, CURLOPT_HEADER, false);
  }

  public function __destruct()
  {
    curl_close($this->ch);
  }

  public function headers(array $headers)
  {
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
  }

  public function log(string $logFile = SYS_DIR .'/log/curl.log')
  {
    $this->logFile = fopen($logFile, 'w');
    curl_setopt($this->ch, CURLOPT_VERBOSE, true);
    curl_setopt($this->ch, CURLOPT_STDERR, $this->logFile);
  }

  // ignore proxy on localhost
  public function noProxy()
  {
    curl_setopt($this->ch, CURLOPT_PROXY, '');
  }

  // ustaw folder zdalny
  public function get(string $url)
  {
    $this->url = $url;
    [$protocol] = explode(':', $url);

    if ($protocol == 'https') {
      curl_setopt($this->ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
      curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    curl_setopt($this->ch, CURLOPT_URL, $url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($this->ch);

    if ($this->logFile) {
      fclose($this->logFile);
    }

    if (curl_errno($this->ch)) {
      $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
      $message = curl_error($this->ch) .' ' .(new Status($code, 'pl'))->message();
      return $this->fault('CURL fault', $message);
    }
    return $this->decode($response);
  }

  // decode json data from string
  // returns response object or fault object
  public function decode(string $response)
  {
    [$json] = explode('<!-- Tracy', $response);
    if (!$response || !trim($json)) {
      return $this->fault('Service error', 'Empty response');
    }

    $rdata = json_decode($json);
    if ($rdata === null) {
      return $this->fault('JSON fault', $this->jsonError(), $response);
    } elseif (@$rdata->fault) {
      Log::error('Fault in response', $rdata);
    }

    if (isset($json[1]) && is_object($rdata)) {
      $rdata->tracy = 'detected'; // informacja o usunięciu kodu Tracy
    }
    return $rdata;
  }

  // get structured fault info
  private function fault($origin, $message, $data = null)
  {
    $fault = [
      'origin' => $origin,
      'url' => $this->url,
      'message' => $message,
    ];
    if ($data) {
      $fault['data'] = $data;
    }
    Log::error('JSON API fault', $fault);

    return (object) [
      'fault' => (object) $fault,
    ];
  }

}
