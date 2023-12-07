<?php

namespace Andygrond\Hugonette;

/** JSON API Provider abstract class for Hugonette
 * Use ApiProvider extension class for API entry point
 * 
 * @author Andygrond 2023
**/

use Andygrond\Hugonette\Env;

abstract class ApiProvider extends Presenter
{
  protected $status = [
    'code' => 200,
    'message' => 'OK',
  ];

  /** Define this method in the child class if needed
   * Input data validation and naming
   * @param array $segm request URL segments
   * @return bool request syntax is valid
   */
  protected function request(array $segm)
  {
    return true;
  }

  /** Define this method in the child class if needed
   * Authentication & authorization
   * @return bool request is authorized
   */
  protected function authorize()
  {
    return true;
  }

  /** Define this method in the child class
   * Get response payload
   * @return mixed response payload | error message | false | null
   */
  abstract protected function response();

  /** Define this method in the child class if needed
   * Full response
   * @param mixed $data response payload or error message
   * @return mixed full response
   */
  protected function envelope($data)
  {
    return $data;
  }

  // Provider default class called in Route
  protected function default()
  {
    try {
      if ($req = $this->request(Env::get('request.segments'))) {
        if ($this->authorize()) {
          $data = $this->response();

          if (is_string($data)) {
            $this->setStatus(400, $data);
          } elseif ($data === false) {
            $this->setStatus(406, 'Unknown resource type');
          } elseif ($data === null) {
            $this->setStatus(404, 'Resource not found');
          } else {
            return $this->envelope($data);
          }
        } else {
          $this->setStatus(401, 'Access denied');
        }
      } else {
        $this->setStatus(400, 'Invalid syntax');
      }
    } catch (\Throwable $t) {
      Log::error('Provider error: ' .$t);
      $this->setStatus(500, $this->errorMessage($t));
    }

    return [
      'status' => $this->status,
    ];
  }

  /** Typical resource calculation or friendly info on resource type mismatch
   * Presumption: all methods of $obj not starting with '_' are resource types
   * @param $obj data provider object for present resource type
   */
  protected function resource($obj, $type, $id = null)
  {
    if ($type && method_exists($obj, $type) && $type[0] != '_') {
      return $obj->$type($id);

    } else {
      $methods = array_filter(get_class_methods($obj), function($method){
        return $method[0] != '_';
      });
      $this->setStatus(406, 'Possible resource types: ' .implode('|', $methods));
    }
  }

  protected function setStatus($code, $message)
  {
    http_response_code($code);
    $this->status['code'] = $code;
    $this->status['message'] = $message;
    Log::info('API status', $this->status);
  }

  protected function errorMessage($t)
  {
    return $t->getMessage() .' in ' .$t->getFile() .':' .$t->getLine();
  }

}
