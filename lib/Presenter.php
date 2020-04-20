<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
 * @author Andygrond 2020
**/

class Presenter
{
  private $method;        // presenter method determined in route

  protected $page;      // navigation data for response processing
  protected $template;  // template set in presenter extension
  protected $view;      // view type set in presenter extension

  public function __construct(string $method = 'default')
  {
    $this->method = $method;
  }

  // return model data using presenter method declared in router
  // presenter method will return an array of model data
  // when this case appears not relevant - presenter extended method will return false
  public function run(\stdClass $page)
  {
    $this->page = $page;

    $model = $this->{$this->method}();	// presenter method call

    if ($model !== false) { // only when passed by presenter
      if (Log::$debug) {
        bdump($this->page, 'page');
        bdump($model, 'model');
      }

      $view = $this->view ?: $page->view;
      $template = $this->template ?? $page->template;
      $template = ($view == 'json')? '' : $page->publishBase .$template;
      (new View($template, $page->cacheLatte))->$view($model);

      exit;
    }
    // else bypass to the next route
  }

  // data or file transfer
  public function upload(\stdClass $page)
  {
    
  }

  // redirect @$to if URI simply starts from $pattern or $pattern is empty
  // @$permanent in Presenter defaults to http code 302 Found
  public function redirect(string $to, bool $permanent = false)
  {
    $code = $permanent? 301 : 302;
    Log::info($code .' Redirected to: ' .$to);
    header('Location: ' .$to, true, $code);

    exit;
  }

  // upload content
  public function upContent(string $content, string $filename)
  {
    $this->upHeaders($filename);
    echo $content;

    exit;
  }

  // upload content
  public function upJson(string $content)
  {
    $this->upHeaders('json');
    echo $content;

    exit;
  }

  // upload file
  public function upFile(string $sourcePath, string $filename = null)
  {
    if (is_file($sourcePath)) {
      $this->upHeaders($filename?? );
      readfile($sourcePath);
    } else {
      http_response_code(404);
      Log::warning('Uploaded file not found: ' .$sourcePath);
    }

    exit;
  }

}
