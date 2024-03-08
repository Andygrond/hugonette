<?php

namespace Andygrond\Hugonette\Access;

/* JSON API data coder and decoder with http(s) transport
 * @author Andygrond 2020
**/

use Andygrond\Hugonette\Traits\JsonErrorTrait;
use Andygrond\Hugonette\Log;

class JsonApi
{
  use JsonErrorTrait;

  protected $ch;  // curl handle
  protected $url; // last url request

  public function __construct()
  {
    $this->ch = curl_init();

    curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
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
    return curl_errno($this->ch)? $this->fault('CURL fault', curl_error($this->ch)) : $this->decode($response);
  }

  // decode json data from string
  // returns response object or fault object
  public function decode(string $response)
  {
    if (!$response) {
      return $this->fault('Service error', 'Empty response');
    }

    $json = @explode('<!-- Tracy', $response);
    $rdata = json_decode($json[0]);

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
