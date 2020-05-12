<?php

namespace Andygrond\Hugonette;

/* MVP Upload View strategy for Hugonette
* @author Andygrond 2020
**/

class UploadView implements View
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

// upload file
// $model['destinationFile'] suggested 'filename.ext' of received file or '.ext' when saving file is not intended
// $model['inline'] true: try to display the content, false: try save the file
// $model['sourceFile'] uploaded file name or $model['data'] uploaded content
  public function view(array $model, \stdClass $page = null)
  {
    if ($model === false)
      return;

    $disposition = @$model['inline']? 'inline' : 'attachment';
    $file = $model['destinationFile'];

    if ($pos = strrpos($file, '.')) {
      $disposition .= '; filename=' .$file;
    }
    $extension = substr($file, $pos+1);
    $mimeType = $this->mimeTypes[$extension]?? 'application/octet-stream';

    $file = @$model['sourceFile'];
    if ($file && !is_file($file)) {
      http_response_code(404);
      Log::warning('Uploaded file not found: ' .$file);

    } else {
      header('Cache-Control: no-cache');
      header('Content-Type: ' .$mimeType);
      header('Content-Disposition: ' .$disposition);

      if ($file) {
        readfile($file);
      } elseif (@$model['sourceData']) {
        echo $model['sourceData'];
      } else {
        throw new \InvalidArgumentException("Upload source not specified.");
      }
    }

    exit;
  }

}
