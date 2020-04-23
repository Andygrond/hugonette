<?php

namespace Andygrond\Hugonette;

/* Upload methods for Hugonette
 * @author Andygrond 2020
**/

class Upload
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

  // send headers
  // $fileExt = suggested 'filename.ext' of received file or 'ext' when saving file is not intended
  // $inline = browser tries to display the content, otherwise tries to save the file
  public function __construct(string $fileExt, bool $inline = true)
  {
    header('Cache-Control: no-cache');
    $disposition = $inline? 'inline' : 'attachment';

    if ($pos = strrpos($fileExt, '.')) {
      $disposition .= '; filename=' .$fileExt);
      $extension = substr($fileExt, $pos);
    } else {
      $extension = $fileExt;
    }

    $mimeType = $this->mimeTypes[$extension]?? 'application/octet-stream';
    header('Content-Type: ' .$mimeType);
    header('Content-Disposition: ' .$disposition);
  }

  // upload content
  public function content(string $content)
  {
    echo $content;
  }

  // upload JSON content
  public function json($data)
  {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
  }

  // upload file
  public function file(string $sourceFile)
  {
    if (is_file($sourceFile)) {
      readfile($sourceFile);
    } else {
      http_response_code(404);
      Log::warning('Uploaded file not found: ' .$sourceFile);
    }
  }

}
