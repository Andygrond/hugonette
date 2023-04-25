<?php

namespace Andygrond\Hugonette;

/** JSON API Presenter abstract class for Hugonette
 * ApiPresenter extension class must provide methods: validRequest and serviceProvider
 * 
 * @author Andygrond 2023
**/


abstract class ApiPresenter extends Presenter
{
  private $status = [];

  /** Input data validation and naming
   * For standard usage: define 'type' and 'id'
   * @return request args
   */
  abstract protected function validRequest():array;

  /** Service provider object injection
   * methods of serviceProvider class return model data for a resource type
   * @param req request args
   */
  abstract protected function serviceProvider(array $req);

  // Provider default class called in Route
  protected function default()
  {
    try {
      if ($req = $this->validRequest()) {
        $data = $this->getResponse($req);

        if (is_string($data)) {
          $this->setStatus(400, $data);
        } elseif ($data === false) {
          $this->setStatus(406, 'Unknown resource type');
        } elseif ($data === null) {
          $this->setStatus(404, 'Resource not found');
        } else {
          return $data;
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

  /** Gets response data
   * Presumption: defined $req 'type' and 'id'
   * For non standard usage redefine this method
   * @param req request args
   */
  protected function getResponse(array $req)
  {
    $obj = $this->serviceProvider($req);
    $type = $req['type'];
    if (!$type || !method_exists($obj, $type)) {
      $this->allowedTypes($obj);
      return false;
    }

    return $obj->$type($req['id']);
  }

  /** Sets friendly info on resource type mismatch
   * Presumption: all methods of $obj not starting with '_' are resource types
   * @param obj data provider object for present resource type
   */
  protected function allowedTypes(object $obj)
  {
    $methods = array_filter(get_class_methods($obj), function($method){
      return $method[0] != '_';
    });
    $this->setStatus(406, 'Possible resource types: ' .implode('|', $methods));
  }

  protected function setStatus($code, $message)
  {
    http_response_code($code);
    $this->status = [
      'code' => $code,
      'message' => $message,
    ];
    Log::info('API status', $this->status);
  }

  protected function errorMessage($t)
  {
    return $t->getMessage() .' in ' .$t->getFile() .':' .$t->getLine();
  }

}
