<?php

namespace Andygrond\Hugonette\Helpers;

/**
 * Mail sender
 * @dependency Swift Mailer
 * @author Andygrond 2021
 */

use Andygrond\Hugonette\Log;
use Andygrond\Hugonette\Env;
use Andygrond\Hugonette\Helpers\Decrypt;
use Latte\Engine;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Swift_Attachment;

class Mailer
{
  private $mailer;  // Swift Mailer instance
  private $latte;   // Latte Engine instance
  private $from;
  private $body;
  private $template;

  public $message;  // Swift Message instance
  public $lastError;
  public $lastRejected;

/**
 * @param from - array [mail => name] or string mail
 * @param smtp - credentials array [server, user, pass]
 */
  public function __construct($from, $profile)
  {
    $file = Env::get('hidden.file.smtp');
    $access = Decrypt::data($file)->get($profile);

    $user = $access->domain? $access->domain .'\\' : '';
    $user .= $access->user;

    $transport = (new Swift_SmtpTransport($access->smtpServer, 25))
      ->setUsername($user)
      ->setPassword($access->pass);

    $this->mailer = new Swift_Mailer($transport);
    $this->from = $from;

    $latte = new Engine;
    $filters = Env::get('base.system') .'/app/filters.php';
    if (file_exists($filters)) {
      include($filters);
    }
    $latte->setTempDirectory(Env::get('base.system') .'/temp/latte');
    $this->latte = $latte;
  }

  /** Get new Swift Message
  * @param subject - mail subject
  */
  public function message(string $subject)
  {
    $this->message = (new Swift_Message($subject))->setFrom($this->from);
  }

  /** Get new Swift Message
  * @param template - template file name
  */
  public function template(string $template)
  {
    $this->template = Env::get('base.template') .$template;
    if (!file_exists($this->template)) {
      Log::error('Mailer: template not found: ' .$template);
    }
  }

  public function to($to)
  {
    $this->message->setTo($to);
  }

  /** Format message body according to model
  * @param model - data will be passed to template
  * @param replace - pairs for replacing in rendered string
  */
  public function body($model, array $replace = [])
  {
    $body = $this->latte->renderToString($this->template, $model);
    if ($replace) {
      $body = strtr($body, $replace);
    }
//    exit(htmlentities($body));
    $this->message->setBody($body, 'text/html');
  }

  /** Attach the file to the message
  * @param file - path to the file
  * @param name - string: set the file name / boolean: takes the role of @param inline
  * @param inline - display the image within the message
  * @return cid - to embed inline image: <img src="cid" ... />
  */
  public function attach(string $file, $name = false, bool $inline = false): string
  {
    $attachment = Swift_Attachment::fromPath($file);
    if (is_bool($name)) {
      $inline = $name;
    } elseif ($name) {
      $attachment->setFilename($name);
    }
    if($inline) {
      return $this->message->embed($attachment->setDisposition('inline'));
    } else {
      $this->message->attach($attachment);
    }
  }

  /** Attach the data content to the message
  * @param data - data string
  * @param name - set the file name
  * @param contentType - content type of the data
  * @param inline - display the image within the message
  */
  public function attachData(string $data, string $name, string $contentType, bool $inline = false)
  {
    $attachment = new Swift_Attachment($data, $name, $contentType);
    if($inline) {
      $attachment->setDisposition('inline');
    }
    if($inline) {
      return $this->message->embed($attachment->setDisposition('inline'));
    } else {
      $this->message->attach($attachment);
    }
  }

  /** Send message
  */
  public function send()
  {
    try {
      $response = $this->mailer->send($this->message, $this->lastRejected);
      if (!$response) {
        $amount = count($this->lastRejected);
        Log::warning("Mailer: Mail was not delivered to $amount recipients", $this->lastRejected);
      }
      return $response;

    } catch (\Exception $e) {
      $this->lastError = trim($e->getMessage());
      Log::error('Mailer: Mail not sent: ' .$this->lastError);
    }
  }

}
