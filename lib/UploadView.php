<?php

namespace Andygrond\Hugonette;

/* MVP Upload View strategy for Hugonette
* @author Andygrond 2020
**/

use stdClass;

class UploadView
{
  // default MIME types for uploading
  private $mimeTypes = [
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

  private $page;

  // send headers
  // $fileExt = suggested 'filename.ext' of received file or 'ext' when saving file is not intended
  // $inline = browser tries to display the content, otherwise tries to save the file
  public function __construct(stdClass $page)
  {
    $this->page = $page;
  }

  public function view(array $model)
  {
    //    string $fileExt, bool $inline = true
      $disposition = $inline? 'inline' : 'attachment';

      if ($pos = strrpos($fileExt, '.')) {
        $disposition .= '; filename=' .$fileExt);
        $extension = substr($fileExt, $pos);
      } else {
        $extension = $fileExt;
      }

      $mimeType = $this->mimeTypes[$extension]?? 'application/octet-stream';
      header('Cache-Control: no-cache');
      header('Content-Type: ' .$mimeType);
      header('Content-Disposition: ' .$disposition);
  }

  // upload content
  private function content(string $content)
  {
    echo $content;
  }

  // upload file
  private function file(string $sourceFile)
  {
    if (is_file($sourceFile)) {
      readfile($sourceFile);
    } else {
      http_response_code(404);
      Log::warning('Uploaded file not found: ' .$sourceFile);
    }
  }

}
