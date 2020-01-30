<?php

namespace Andygrond\Hugonette;

/* HTTP error status codes
 * @author Andygrond 2019
**/

class Status
{
	// configuration data
	private static $cfg = [
		'lang' => LANG,
	];

	private static $codes = [
	
// 1xx Informational responses
		100 => [
			'en' => 'Continue',
		//	'desc' => 'Client should continue with request.'
		],
		101 => [
			'en' => 'Switching Protocols',
		//	'desc' => 'Server is switching protocols.'
		],
		102 => [
			'en' => 'Processing',
		//	'desc' => 'Server has received and is processing the request.'
		],
		103 => [
			'en' => 'Early Hints',
		//	'desc' => 'Used to return some response headers before final HTTP message'
		],

// 2xx Success
		200 => [
			'en' => 'OK',
		//	'desc' => 'The request was fulfilled.'
		],
		201 => [
			'en' => 'Created',
		//	'desc' => 'The request has been fulfilled, resulting in the creation of a new resource.'
		],
		202 => [
			'en' => 'Accepted',
		//	'desc' => 'The request has been accepted for processing, but the processing has not been completed. The request might or might not be eventually acted upon, and may be disallowed when processing occurs.'
		],
		203 => [
			'en' => 'Non-Authoritative Information',
		//	'desc' => 'Non-Authoritative Information (since HTTP/1.1).'
		],
		204 => [
			'en' => 'No Content',
		//	'desc' => 'The server successfully processed the request and is not returning any content.'
		],
		205 => [
			'en' => 'Reset Content',
		//	'desc' => 'The server successfully processed the request, but is not returning any content. Unlike a 204 response, this response requires that the requester reset the document view.'
		],
		206 => [
			'en' => 'Partial Content',
		//	'desc' => 'The server is delivering only part of the resource (byte serving) due to a range header sent by the client. The range header is used by HTTP clients to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams'
		],
		207 => [
			'en' => 'Multi-Status',
		//	'desc' => 'The message body that follows is by default an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.'
		],
		208 => [
			'en' => 'Already Reported',
		//	'desc' => 'The message body that follows is by default an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.'
		],
		226 => [
			'en' => 'IM Used',
		//	'desc' => 'The server has fulfilled a request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.'
		],

// 3xx Redirection
		300 => [
			'en' => 'Multiple Choices',
		//	'desc' => 'Indicates multiple options for the resource from which the client may choose (via agent-driven content negotiation). For example, this code could be used to present multiple video format options, to list files with different filename extensions, or to suggest word-sense disambiguation.'
		],
		301 => [
			'en' => 'Moved Permanently',
		//	'desc' => 'The request has been fulfilled, resulting in the creation of a new resource.'
		],
		302 => [
			'en' => 'Found',
		//	'desc' => 'Tells the client to look at (browse to) another url. 302 has been superseded by 303 and 307. The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"), but popular browsers implemented 302 with the functionality of a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours. However, some Web applications and frameworks use the 302 status code as if it were the 303.'
		],
		303 => [
			'en' => 'See Other',
		//	'desc' => 'The response to the request can be found under another URI using the GET method. When received in response to a POST (or PUT/DELETE), the client should presume that the server has received the data and should issue a new GET request to the given URI.'
		],
		304 => [
			'en' => 'Not Modified',
		//	'desc' => 'Indicates that the resource has not been modified since the version specified by the request headers If-Modified-Since or If-None-Match. In such case, there is no need to retransmit the resource since the client still has a previously-downloaded copy.'
		],
		305 => [
			'en' => 'Use Proxy',
		],
		306 => [
			'en' => 'Switch Proxy',
		],
		307 => [
			'en' => 'Temporary Redirect',
		//	'desc' => 'In this case, the request should be repeated with another URI; however, future requests should still use the original URI. In contrast to how 302 was historically implemented, the request method is not allowed to be changed when reissuing the original request. For example, a POST request should be repeated using another POST request.'
		],
		308 => [
			'en' => 'Permanent Redirect',
		//	'desc' => 'The request and all future requests should be repeated using another URI. 307 and 308 parallel the behaviors of 302 and 301, but do not allow the HTTP method to change. So, for example, submitting a form to a permanently redirected resource may continue smoothly.'
		],

// 4xx Client errors
		400 => [
			'en' => 'Bad Request',
			'pl' => 'Niewłaściwe wywołanie',
		//	'desc' => 'The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).'
		],
		401 => [
			'en' => 'Unauthorized',
			'pl' => 'Nie posiadasz uprawnień do tej informacji',
		//	'desc' => 'Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided. The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource.'
		],
		402 => [
			'en' => 'Payment Required',
		//	'desc' => 'Reserved for future use. The original intention was that this code might be used as part of some form of digital cash or micropayment scheme.'
		],
		403 => [
			'en' => 'Forbidden',
			'pl' => 'Dostęp zabroniony, spróbuj się zalogować',
		//	'desc' => 'The request was valid, but the server is refusing action. The user might not have the necessary permissions for a resource, or may need an account of some sort.'
		],
		404 => [
			'en' => 'Not Found',
			'pl' => 'Strona pod tym adresem nie istnieje',
		//	'desc' => 'The requested resource could not be found but may be available in the future. Subsequent requests by the client are permissible.'
		],
		405 => [
			'en' => 'Method Not Allowed',
		//	'desc' => 'A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.'
		],
		406 => [
			'en' => 'Not Acceptable',
		//	'desc' => 'The requested resource is capable of generating only content not acceptable according to the Accept headers sent in the request'
		],
		407 => [
			'en' => 'Proxy Authentication Required',
		],
		408 => [
			'en' => 'Request Timeout',
		//	'desc' => 'The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time.'
		],
		409 => [
			'en' => 'Conflict',
		//	'desc' => 'Indicates that the request could not be processed because of conflict in the current state of the resource, such as an edit conflict between multiple simultaneous updates.'
		],
		410 => [
			'en' => 'Gone',
		//	'desc' => 'Indicates that the resource requested is no longer available and will not be available again. This should be used when a resource has been intentionally removed and the resource should be purged. Clients such as search engines should remove the resource from their indices. Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.'
		],
		411 => [
			'en' => 'Length Required',
		//	'desc' => 'The request did not specify the length of its content, which is required by the requested resource'
		],
		412 => [
			'en' => 'Precondition Failed',
		//	'desc' => 'The server does not meet one of the preconditions that the requester put on the request.'
		],
		413 => [
			'en' => 'Payload Too Large',
		//	'desc' => 'The request is larger than the server is willing or able to process.'
		],
		414 => [
			'en' => 'URI Too Long',
		//	'desc' => 'The URI provided was too long for the server to process. Often the result of too much data being encoded as a query-string of a GET request, in which case it should be converted to a POST request.'
		],
		415 => [
			'en' => 'Unsupported Media Type',
		//	'desc' => 'The request entity has a media type which the server or resource does not support. For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.'
		],
		416 => [
			'en' => 'Range Not Satisfiable',
		//	'desc' => 'The client has asked for a portion of the file (byte serving), but the server cannot supply that portion. For example, if the client asked for a part of the file that lies beyond the end of the file.'
		],
		417 => [
			'en' => 'Expectation Failed',
		//	'desc' => 'The server cannot meet the requirements of the Expect request-header field.'
		],
		418 => [
			'en' => 'I\'m a teapot',
		//	'desc' => 'April Fools\' joke in RFC 2324, Hyper Text Coffee Pot Control Protocol'
		],
		421 => [
			'en' => 'Misdirected Request',
		//	'desc' => 'The request was directed at a server that is not able to produce a response (for example because of connection reuse).'
		],
		422 => [
			'en' => 'Unprocessable Entity',
		//	'desc' => 'The request was well-formed but was unable to be followed due to semantic errors.'
		],
		423 => [
			'en' => 'Locked',
		//	'desc' => 'The resource that is being accessed is locked.'
		],
		424 => [
			'en' => 'Failed Dependency',
		//	'desc' => ''
		],
		426 => [
			'en' => 'Upgrade Required',
		//	'desc' => 'The client should switch to a different protocol such as TLS/1.0, given in the Upgrade header field.'
		],
		428 => [
			'en' => 'Precondition Required',
		//	'desc' => 'The origin server requires the request to be conditional. Intended to prevent the lost update problem, where a client GETs a resource state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict.'
		],
		429 => [
			'en' => 'Too Many Requests',
		//	'desc' => 'The user has sent too many requests in a given amount of time. Intended for use with rate-limiting schemes.'
		],
		431 => [
			'en' => 'Request Header Fields Too Large',
		//	'desc' => 'The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.'
		],
		451 => [
			'en' => 'Unavailable For Legal Reasons',
		//	'desc' => 'A server operator has received a legal demand to deny access to a resource or to a set of resources that includes the requested resource.'
		],

// 5xx Server errors
		500 => [
			'en' => 'Internal Server Error',
			'pl' => 'Przepraszamy, wystapił błąd wewnętrzny serwera i strona nie może być wyświetlona',
		//	'desc' => 'A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.'
		],
		501 => [
			'en' => 'Not Implemented',
			'pl' => 'Przepraszamy, ta część witryny nie została jeszcze wdrożona',
		//	'desc' => 'The server either does not recognize the request method, or it lacks the ability to fulfil the request. Usually this implies future availability (e.g., a new feature of a web-service API)'
		],
		502 => [
			'en' => 'Bad Gateway',
		//	'desc' => 'The server was acting as a gateway or proxy and received an invalid response from the upstream server.'
		],
		503 => [
			'en' => 'Service Unavailable',
			'pl' => 'Przepraszamy, serwer jest chwilowo niedostępny z powodu aktualizacji.',
		//	'desc' => 'The server is currently unavailable (because it is overloaded or down for maintenance). Generally, this is a temporary state.'
		],
		504 => [
			'en' => 'Gateway Timeout',
		//	'desc' => 'The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.'
		],
		505 => [
			'en' => 'HTTP Version Not Supported',
		//	'desc' => 'The server does not support the HTTP protocol version used in the request.'
		],
		506 => [
			'en' => 'Variant Also Negotiates',
		//	'desc' => 'Transparent content negotiation for the request results in a circular reference.'
		],
		507 => [
			'en' => 'Insufficient Storage',
		//	'desc' => 'The server is unable to store the representation needed to complete the request.'
		],
		508 => [
			'en' => 'Loop Detected',
		//	'desc' => 'The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).'
		],
		510 => [
			'en' => 'Not Extended',
		//	'desc' => 'Further extensions to the request are required for the server to fulfil it.'
		],
		511 => [
			'en' => 'Network Authentication Required',
		],
	];

	public static function message($code)
	{
		if (isset(self::$codes[$code])) {
			return self::$codes[$code][self::$cfg['lang']] ?? self::$codes[$code]['en'];
		}
		return false;
	}

}
