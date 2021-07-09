<?php

namespace Andygrond\Hugonette\Views;

/* MVP Upload View strategy for Hugonette
* @author Andygrond 2020
**/

use Latte\Engine;
use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;

class UploadView implements ViewInterface
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

/** upload file based on Env $upload array
  * @param upload.inline true: try to display the content, default: try save the file
  * @param upload.destinationFile - suggest 'filename.ext' of file or '.ext' to send Content-Type header
  * @param upload.sourceFile uploaded file name or upload.sourceData'] uploaded content
  * @param upload.data with latte template
  */
  public function __construct(array $model)
  {
    $upload = Env::get('upload');
    $mimeType = 'application/octet-stream';
    $disposition = @$upload['inline']? 'inline' : 'attachment';

    // problem detection first
    if (@$upload['sourceFile']) {
      if (!is_file($upload['sourceFile'])) {
        http_response_code(404);
        Log::warning('Uploaded file not found: ' .$upload['sourceFile']);
        return false;
      }
    }

    // headers
    if ($file = @$upload['destinationFile']) {
      $pos = strrpos($file, '.');
      if ($pos !== false) {
        $disposition .= '; filename=' .$file;
        $extension = substr($file, $pos+1);
        $mimeType = $this->mimeTypes[$extension]?? 'application/octet-stream';
      }
    }
    header('Cache-Control: no-cache');
    header('Content-Type: ' .$mimeType);
    header('Content-Disposition: ' .$disposition);

    // content
    if (@$upload['sourceFile']) {
      readfile($upload['sourceFile']);

    } elseif (@$upload['sourceData']) {
      echo $model['sourceData'];

    } elseif (@$upload['template']) { // render in Latte
      $latte = new Engine;
      $latte->setTempDirectory(Env::get('base.system') .'/temp/latte');
      $template = Env::get('base.template') .$upload['template'];
      $latte->render($template, $model);

    } else {
      throw new \RuntimeException("Upload source not specified.");
    }
  }

}
