<?php

namespace Andygrond\Hugonette;

/* MVP Presenter class for Hugonette
* @author Andygrond 2020
**/

class Presenter
{
  private $method;        // presenter method determined in route
  private $mimeTypes = [  // default MIME types for uploading
    'txt' => 'text/plain;charset=UTF-8',
    'csv' => 'text/csv;charset=UTF-8',
    'html' => 'text/html',
    'xml' => 'text/xml',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'zip' => 'application/zip',
    'pdf' => 'application/pdf',
    'json' => 'application/json',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml',
    'gif' => 'image/gif',
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
  ];

  protected $page;      // navigation data for response processing
  protected $template;  // template set in presenter method
  protected $view;      // view type set in presenter method

  public function __construct(string $method = 'default')
  {
    $this->method = $method;
  }

  // return model data using presenter method declared in router
  // presenter method will return an array of model data
  // when this case appears not relevant - presenter method will return false
  public function run(\stdClass $page)
  {
    $this->page = $page;

    $model = $this->{$this->method}();	// presenter method call

    if ($model !== false) { // only when passed by presenter
      if (Log::$debug) {
        bdump($this->page, 'page');
        bdump($model, 'model');
      }

      $template = $this->template ?: @$page->template;
      $view = $this->view ?: @$page->view;
      (new View($page->publishBase .$template, $page->cacheLatte))->$view($model);

      exit;
    }
    // else bypass to the next route
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
  public function upContent(string $content, string $filename, bool $inline = false, string $mimeType = null)
  {
    $this->upHeaders($filename, $inline, $mimeType);
    echo $content;

    exit;
  }

  // upload file
  public function upFile(string $sourcePath, string $filename = null, bool $inline = false, string $mimeType = null)
  {
    $this->upHeaders($filename?? $sourcePath, $inline, $mimeType);

    if (is_file($sourcePath)) {
      readfile($sourcePath);
    } else {
      http_response_code(404);
      Log::warning('Uploaded file not found: ' .$sourcePath);
    }

    exit;
  }

  private function upHeaders(string $filename, bool $inline, string $mimeType)
  {
    $file = pathinfo($filename);
    $disposition = $inline? 'inline' : 'attachment';

    if (!$mimeType) {
      $mimeType = @$this->mimeTypes[$file['extension']]?? 'application/octet-stream';
    }

    header('Content-Type: ' .$mimeType);
    header('Content-Disposition: ' .$disposition .'; filename=' .$file['basename']);
    header('Pragma: no-cache');
  }

}
