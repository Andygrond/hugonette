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
  // $fileExt = 'filename.ext' of received file or 'ext' only to open in browser
  public function __construct(string $fileExt)
  {
    header('Cache-Control: no-cache');

    if ($pos = strrpos($fileExt, '.')) {
      $this->mimeType(substr($fileExt, $pos));
      header('Content-Disposition: attachment; filename=' .$fileExt);
    } else {
      $this->mimeType($fileExt);
      header('Content-Disposition: inline');
    }
  }

  // send mime type header
  private function mimeType(string $ext)
  {
    $mimeType = $this->mimeTypes[$ext]?? 'application/octet-stream';
    header('Content-Type: ' .$mimeType);
  }

  // upload content
  public function content(string $content)
  {
    echo $content;

    exit;
  }

  // upload JSON content
  public function json(array $data)
  {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);

    exit;
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

    exit;
  }

}
