<?php

namespace Andygrond\Hugonette\Traits;

/* JSON Error human friendly
 * @author Andygrond 2020
**/

trait Fault
{
  private static $httpStatusCodes = [

// 1xx Informational responses
    100 => [
      's' => 'Continue',
    ],
    101 => [  // in response to an Upgrade request header
      's' => 'Switching Protocols',
    ],
    102 => [
      's' => 'Processing',
      'en' => 'Server has received and is processing the request',
    ],
    103 => [  // primarily intended to be used with the Link header
      's' => 'Early Hints',
    ],

// 2xx Success
    200 => [
      's' => 'OK',
    ],
    201 => [
      's' => 'Created',
    ],
    202 => [  // when another process or server handles the request, or for batch processing
      's' => 'Accepted',
    ],
    203 => [  // not origin data source: mirror or backup
      's' => 'Non-Authoritative Information',
    ],
    204 => [
      's' => 'No Content',
      'en' => 'Request processed successfully',
    ],
    205 => [
      's' => 'Reset Content',
      'en' => 'Request processed successfully - reset the document view',
    ],
    206 => [  // when a Range header is sent by the client to request only part of a resource
      's' => 'Partial Content',
    ],
    207 => [  // WebDAV
      's' => 'Multi-Status',
      'en' => 'Message body contain separate response codes',
    ],
    208 => [  // WebDAV
      's' => 'Already Reported',
    ],
    226 => [  // HTTP Delta encoding
      's' => 'IM Used',
    ],

// 3xx Redirection
    300 => [
      's' => 'Multiple Choices',
    ],
    301 => [
      's' => 'Moved Permanently',
    ],
    302 => [  // URI changed temporarily - specified by 303 and 307
      's' => 'Found',
    ],
    303 => [  // response to the request can be found under another URI using GET method
      's' => 'See Other',
    ],
    304 => [  // with request headers If-Modified-Since or If-None-Match
      's' => 'Not Modified',
    ],
    307 => [  // the request should be repeated with another URI but the same HTTP method (e.g. POST)
      's' => 'Temporary Redirect',
    ],
    308 => [  // the request and all future requests should be repeated using another URI but the same HTTP method (e.g. POST)
      's' => 'Permanent Redirect',
    ],

// 4xx Client errors
    400 => [
      's' => 'Bad Request',
      'en' => 'Invalid syntax',
      'pl' => 'Niepoprawne wywołanie',
    ],
    401 => [  // client is unauthenticated - must authenticate itself
      's' => 'Unauthorized',
      'en' => 'Authenticate yourself to get the response',
      'pl' => 'Zaloguj się aby uzyskać dostęp do zasobu',
    ],
    402 => [  // no standard exists
      's' => 'Payment Required',
    ],
    403 => [  // client's identity is known but he is unauthorized
      's' => 'Forbidden',
      'en' => 'You do not have access rights to the content',
      'pl' => 'Nie posiadasz uprawnień do tej informacji',
    ],
    404 => [  // URL is not recognized or URL is valid but the resource does not exist
      's' => 'Not Found',
      'en' => 'The web page at this address does not exist',
      'pl' => 'Strona pod tym adresem nie istnieje',
    ],
    405 => [
      's' => 'Method Not Allowed',
      'en' => 'HTTP request method is not supported for the requested resource',
    ],
    406 => [
      's' => 'Not Acceptable',
      'en' => 'Content that conforms to the given criteria can not be found',
    ],
    407 => [
      's' => 'Proxy Authentication Required',
    ],
    408 => [
      's' => 'Request Timeout',
    ],
    409 => [
      's' => 'Conflict',
      'en' => 'The request could not be processed because of conflict in the current state of the resource',
    ],
    410 => [  // 404 can be used
      's' => 'Gone',
      'en' => 'Requested content has been permanently deleted',
    ],
    411 => [  // Content-Length header must be defined
      's' => 'Length Required',
    ],
    412 => [
      's' => 'Precondition Failed',
      'en' => 'Server does not meet the preconditions put on the request',
    ],
    413 => [
      's' => 'Payload Too Large',
      'en' => 'The request is larger than limits defined by server',
    ],
    414 => [
      's' => 'URI Too Long',
    ],
    415 => [
      's' => 'Unsupported Media Type',
    ],
    416 => [  // the range specified by the Range header is outside the size of the target URI's data
      's' => 'Range Not Satisfiable',
    ],
    417 => [  // expectation indicated by the Expect request header field can't be met
      's' => 'Expectation Failed',
    ],
    418 => [  // April Fools joke
      's' => 'I\'m a teapot',
    ],
    421 => [
      's' => 'Misdirected Request',
    ],
    422 => [  // WebDAV
      's' => 'Unprocessable Entity',
    ],
    423 => [  // WebDAV
      's' => 'Locked',
    ],
    424 => [  // WebDAV
      's' => 'Failed Dependency',
      'en' => 'The request failed due to failure of a previous request',
    ],
    425 => [
      's' => 'Too Early',
      'en' => 'Request not processed - expected to be replayed',
    ],
    426 => [  // protocol given in the Upgrade header
      's' => 'Upgrade Required',
      'en' => 'Client should switch to a different protocol',
    ],
    428 => [  // to prevent changing state of resource modified in meantime
      's' => 'Precondition Required',
    ],
    429 => [  // rate limitation
      's' => 'Too Many Requests',
    ],
    431 => [
      's' => 'Request Header Fields Too Large',
    ],
    451 => [  // access denied due to a legal demand
      's' => 'Unavailable For Legal Reasons',
    ],

// 5xx Server errors
    500 => [  // generic error message
      's' => 'Internal Server Error',
      'en' => 'Unexpected error was encountered and the page can not be shown',
      'pl' => 'Wystapił błąd wewnętrzny serwera i strona nie może być wyświetlona',
    ],
    501 => [
      's' => 'Not Implemented',
      'en' => 'This feature of API is not available yet',
      'pl' => 'Ta część witryny nie została jeszcze wdrożona',
    ],
    502 => [  // The server was acting as a gateway or proxy and received an invalid response from the upstream server
      's' => 'Bad Gateway',
    ],
    503 => [
      's' => 'Service Unavailable',
      'en' => 'Server is temporarily down for maintenance',
      'pl' => 'Serwer jest chwilowo niedostępny z powodu aktualizacji',
    ],
    504 => [  // The server was acting as a gateway or proxy and did not receive a timely response from the upstream server
      's' => 'Gateway Timeout',
    ],
    505 => [
      's' => 'HTTP Version Not Supported',
    ],
    506 => [
      's' => 'Variant Also Negotiates',
    ],
    507 => [  // WebDAV
      's' => 'Insufficient Storage',
    ],
    508 => [  // WebDAV
      's' => 'Loop Detected',
    ],
    510 => [  // Further extensions to the request are required for the server to fulfil it
      's' => 'Not Extended',
    ],
    511 => [
      's' => 'Network Authentication Required',
    ],
  ];

  // get the last json error
  public function fault($code, $message = '', $lang = 'en')
  {
    if (isset(self::$httpStatusCodes[$code])) {
      http_response_code($code);
      if (!$message) {
        $message = self::$httpStatusCodes[$code][$lang]?? self::$httpStatusCodes[$code]['s'];
      }
    }

    return [
      'fault' => [
        'code' => $code,
        'message' => $message,
      ]
    ];
  }

}
