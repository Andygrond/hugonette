<?php

namespace Andygrond\Hugonette\Access;

/* SOAP client production class
 * use operation (function) names as methods of $this
 * $result = $this->$operation($params);
 * 2020 Andrzej Grondziowski
**/

use SoapClient;
use SoapFault;
use Andygrond\Hugonette\Log;

class SoapClientLite extends SoapClient
{

  private $wsdl;   // URL for documenting
  private $fault;  // fault info
  private $options = [
    'exceptions' => true, // soap errors throw exceptions
    'trace' => true,
    'cache_wsdl' => WSDL_CACHE_MEMORY,
//    'connection_timeout' => 60,
  ];

  // parse WSDL and configure Web service
  // $forceUrl = URL or boolean (= use $wsdl) to overwrite service address
  public function __construct(string $wsdl, array $options = [], bool $forceUrl = false)
  {
    $this->options = $options + $this->options;
    $this->wsdl = $wsdl;

    try {
      parent::__construct($wsdl, $this->options);
    } catch (SoapFault $fault) {
      $this->fault = $this->getFault($fault);
    }

    if ($forceUrl) {
      if ($forceUrl === true) {
        $forceUrl = strstr($wsdl, '?', true);
      }
      $this->__setLocation($forceUrl);
    }
  }

  // operation call on error condition
  public function __call($oper, $args)
  {
    return [
      'operation' => [
        'name' => $oper,
        'args' => $args,
      ],
      'fault' => $this->fault,
    ];
  }

  // document the last Web service call
  // logLevel = 0:faults only; 2:response; 3:request&response; 4:max_info;
  public function getLast(int $logLevel = 4)
  {
    $debug['wsdl'] = $this->wsdl;
    switch($logLevel) {
      case 4:
        $debug['request']['headers'] = $this->__getLastRequestHeaders();
        $debug['response']['headers'] = $this->__getLastResponseHeaders();
      case 3:
        $debug['request']['body'] = Beautify::xml($this->__getLastRequest());
      case 2:
        $debug['response']['body'] = Beautify::xml($this->__getLastResponse());
      case 1:
    }
    return $debug;
  }

  // get all possible operations
  public function getOperations() {
    return $this->__getFunctions();
  }

  // get web service description
  public function getDescription() {
    return $this->__getTypes();
  }

  // get fault in standard structure
  private function getFault($f)
  {
    $org = $f->getTrace()[1];

    return [
      'message' => $f->getMessage(),
      'caller' => $org['file'] .':' .$org['line'],
    ];
  }

}
